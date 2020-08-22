<?php

class Query
{
    /**
     * @var mysqli
     */
    protected $mysqli;

    /**
     * @param mysqli $mysqli
     */
    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function convertParamsToTypes(array $params = []): string
    {
        $string = '';

        foreach ($params as $param) {
            switch (gettype($param)) {
                case 'string':
                case 'NULL':
                    $string .= 's';
                    break;
                case 'integer':
                    $string .= 'i';
                    break;
                case 'float':
                    $string .= 'd';
                    break;
                default:
                    throw new UnexpectedValueException('Unexpected type: ' . gettype($param));
            }
        }

        return $string;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function select(string $sql, array $params = []): array
    {
        $types = $this->convertParamsToTypes($params);
        return $this->query($sql, $types, $params);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function insert(string $sql, array $params = []): bool
    {
        $types = $this->convertParamsToTypes($params);
        return $this->query($sql, $types, $params);

    }

    /**
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function update(string $sql, array $params = []): bool
    {
        $types = $this->convertParamsToTypes($params);
        return $this->query($sql, $types, $params);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public function delete(string $sql, array $params = []): bool
    {
        $types = $this->convertParamsToTypes($params);
        return $this->query($sql, $types, $params);
    }

    /**
     * @param string $sql
     * @param string $types
     * @param array $params
     * @return array|bool
     * @throws RuntimeException
     */
    public function query(string $sql, string $types = '', array $params = [])
    {
        $stmt = $this->mysqli->prepare($sql);
        if (false === $stmt) {
            throw new RuntimeException(
                'prepare() failed: (' . $this->mysqli->errno . ') ' . $this->mysqli->error
            );
        }

        if (!empty($params)) {
            array_unshift($params, $types);
            if (false === call_user_func_array([$stmt, 'bind_param'], $params)) {
                throw new RuntimeException('bind_param() failed: ' . $stmt->error);
            }
        }

        if (false === $stmt->execute()) {
            throw new RuntimeException('execute() failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();

        if (false !== $result) {
            $return = [];
            while ($row = $result->fetch_object()) {
                $return[] = $row;
            }
        } else {
            $return = false;
            if ($stmt->affected_rows) {
                $return = true;
            }
        }

        $stmt->close();
        return $return;
    }
}

