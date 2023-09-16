<?php

class ResetPassword extends Query
{   
    const NUMBER_CHANGES_PASSWORD_MUST_UNIQUE = 3;
    const RESET_PASS_IMMEDIATE_VALUE = '';

    protected $from = 'reset_password';

    /**
     * { function_description }
     *
     * @param      int     $userID    The user id
     * @param      string  $password  The password
     *
     * @return     bool
     */
    public function validateUpdateUserID(int $userID, string $password) 
    {   
        $passwords = $this->newQuery()
                        ->where('user_id', $userID)
                        ->whereNotPasswordNeedUpdate()
                        ->orderBy($this->from . '.id', 'desc')
                        ->limit(self::NUMBER_CHANGES_PASSWORD_MUST_UNIQUE)
                        ->getColumn($this->from . '.password');
    
        return empty($passwords) || !in_array($password, $passwords);
    }

    /**
     * { function_description }
     *
     * @param      int        $userID    The user id
     * @param      string     $password  The password
     *
     * @throws     Exception  (description)
     *
     * @return     <type>     ( description_of_the_return_value )
     */
    public function edit(int $userID, string $password) 
    {
        if($row = $this->canUpdate($userID)){
            return $this->updateID($row['id'], $password);
        }

        throw new Exception('Không tìm thấy yêu cầu thay đổi mật khẩu');
    }

    /**
     * { function_description }
     *
     * @param      int     $rowID     The row id
     * @param      string  $password  The password
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function updateID(int $rowID, string $password)
    {   
        return $this->newQuery()
                    ->where('id', $rowID)
                    ->update([
                        $this->from . '.password' => $password, 
                        $this->from . '.updated_at' => Carbon\Carbon::now()->format('Y-m-d H:i:s'), 
                        $this->from . '.updated_by' => User::getUser()['id']
                    ]);
    }

    /**
     * { function_description }
     *
     * @param      int     $userID    The row id
     * @param      string  $password  The password
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function store(int $userID, string $password = null)
    {   
        $attrs = [
            $this->from . '.user_id' => $userID,
            $this->from . '.password' => $password, 
            $this->from . '.created_at' => Carbon\Carbon::now()->format('Y-m-d H:i:s'), 
            $this->from . '.created_by' => User::getUser()['id'],
        ];

        if(!is_null($password) && $password !== User::RESET_PASS_IMMEDIATE_TIME) {
            $attrs['updated_at'] = Carbon\Carbon::now()->format('Y-m-d H:i:s');
            $attrs['updated_by'] = User::getUser()['id'];
        }

        return $this->newQuery()->insert($attrs);
    }

    /**
     * Determines ability to update.
     *
     * @param      int   $userID  The user id
     *
     * @return     bool  True if able to update, False otherwise.
     */
    public function canUpdate(int $userID)
    {
        return $this->where('user_id', $userID)
            ->wherePasswordNeedUpdate()
            ->first();
    }

    /**
     * { function_description }
     *
     * @return     static
     */
    private function wherePasswordNeedUpdate()
    {
        return $this->where(function($q) {
                    $q->where($this->from . '.password', self::RESET_PASS_IMMEDIATE_VALUE);
                    $q->orWhereNull($this->from . '.password');
                })
                ->where(function($q) {
                    $q->whereNull($this->from . '.updated_at');
                    $q->orWhereNull($this->from . '.updated_by');
                });
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    private function whereNotPasswordNeedUpdate()
    {
        return $this->whereNot(function($q) {
            $q->wherePasswordNeedUpdate();
        });
    }

    /**
     * { function_description }
     *
     * @param      int     $userID    The user id
     * @param      string  $password  The password
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function editOrNew(int $userID, string $password)
    {
        $row = $this->newQuery()->canUpdate($userID);

        return $row 
                ? $this->updateID($row['id'], $password) 
                : $this->store($userID, $password);
    }
}