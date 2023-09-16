<?php
class UserRole
{
    private static $users = [];
    private $userID;

    /**
     * Khởi tạo một instance mới, nếu tham số userID khác null thì instance được trả lại sẽ làm việc với userID này 
     * Ngược lại sẽ làm việc với user hiện tại
     * Ví dụ: 
     *  $userRole = UserRole::user(1234);
     *  $userRole->getRolesCode();
     *
     * @param      int     $userID  The user id
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function user(int $userID = null)
    {   
        $self = new static();
        $self->userID = $userID;

        return $self;
    }

    /**
     * Gets the user id.
     *
     * @return     <type>  The user id.
     */
    public function getUserID()
    {
        return intval($this->userID > 0 ? $this->userID : User::getUser()['id']);
    }

    /**
     * Lấy tất cả roles của user 
     * Chỉ gọi hàm này nếu cần data là "tươi, mới nhất"(Ví dụ ngay sau khi cập nhật hoặc insert role của user và cần 
     * lấy role của user đó)
     * Nếu không thì nên dùng getRolesCode() vì có caching trong cùng request
     *
     * @param      array   $selects  The selects
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function getAll(array $selects = [])
    {   
        $selects = array_merge($selects, [Query::raw('DISTINCT(UPPER(roles_to_privilege.privilege_code)) AS code')]);

        return Query::from('users_roles')
            ->join('roles', 'roles.id', '=', 'users_roles.role_id')
            ->join('roles_to_privilege', 'roles_to_privilege.role_id', '=', 'roles.id')
            ->where('users_roles.user_id', $this->getUserID())
            ->get($selects);
    }

    /**
     * Gets the roles by user id.
     *
     * @param      int     $userID  The user id
     *
     * @return     <type>  The roles by user id.
     */
    public function getRolesCode()
    {   
        $userID = $this->getUserID();

        if(empty(UserRole::$users[$userID])){
            UserRole::$users[$userID] = array_column($this->getAll(), 'code');
        }

        return UserRole::$users[$userID];
    }

    /**
     * TRUE nếu tồn tại quyền, ngược lại FALSE. 
     * Nếu tham số thứ 2 được đặt là true thì sẽ chỉ trả về TRUE nếu user chỉ có duy nhất quyền đó, ngược lại FALSE. 
     *
     * @param      string  $role   The role
     * @param      bool    $only   The only
     *
     * @return     bool    ( description_of_the_return_value )
     */
    public function has(string $role, bool $only = false)
    {   
        $roles = $this->getRolesCode();

        return in_array($role, $roles) && ($only ? count($roles) === 1 : true); 
    }

    /**
     * TRUE nếu tồn tại quyền và chỉ duy nhất quyền đó, ngược lại FALSE
     *
     * @param      <type>  $role   The role
     */
    public function only($role)
    {
        return $this->has($role, true);
    }

    /**
     * TRUE nếu user hiện tại có một trong số các quyền được truyền vào, ngược lại FALSE 
     *
     * @param      <type>  $roles  The roles
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function some($roles)
    {
        $roles = self::argToArray($roles);
        $rolesNotExists = array_diff($roles, $this->getRolesCode());

        return count($rolesNotExists) != count($roles);
    }

    /**
     * TRUE nếu user hiện tại có tất cả quyền được truyền vào, ngược lại FALSE
     *
     * @param      <type>  $roles  The roles
     *
     * @return     bool    ( description_of_the_return_value )
     */
    public function every($roles)
    {   
        return empty(array_diff(
            self::argToArray($roles), 
            $this->getRolesCode()
        ));
    }

    /**
     * Trả về số lượng quyền
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function count()
    {
        return count($this->getRolesCode());
    }
}