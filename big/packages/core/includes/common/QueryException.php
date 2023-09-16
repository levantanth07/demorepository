<?php

class QueryException extends Exception
{
    private $query = '';
    private $mysqlErrorMessage = '';

    /**
     * Constructs a new instance.
     *
     * @param      string     $message   The message
     * @param      int        $code      The code
     * @param      Throwable  $previous  The previous
     * @param      string     $query     The query
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null, string $query = '', string $mysqlErrorMessage = '') 
    {
        $this->setQuery($query);
        $this->setMysqlErrorMessage($mysqlErrorMessage);

        parent::__construct($message, $code, $previous);
    }

    /**
     * Sets the query.
     *
     * @param      string  $query  The query
     */
    public function setQuery(string $query)
    {
        $this->query = $query;
    }

    /**
     * Gets the query.
     *
     * @return     <type>  The query.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Sets the mysql error message.
     *
     * @param      string  $mysqlErrorMessage  The mysql error message
     */
    public function setMysqlErrorMessage(string $mysqlErrorMessage)
    {
        $this->mysqlErrorMessage = $mysqlErrorMessage;
    }

    /**
     * Gets the mysql error message.
     *
     * @return     <type>  The mysql error message.
     */
    public function getMysqlErrorMessage()
    {
        return $this->mysqlErrorMessage;
    }
}