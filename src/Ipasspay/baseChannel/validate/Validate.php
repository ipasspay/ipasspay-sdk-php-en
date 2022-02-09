<?php

namespace Ipasspay\baseChannel\validate;

class Validate
{

    /**
     * Custom verification type
     * @var array
     */
    protected static $type = [];

    /**
     * Validation type alias
     * @var array
     */
    protected $alias = [
        '>' => 'gt', '>=' => 'egt', '<' => 'lt', '<=' => 'elt', '=' => 'eq', 'same' => 'eq',
    ];

    /**
     * Current validation rule
     * @var array
     */
    protected $rule = [];

    /**
     * Verification prompt
     * @var array
     */
    protected $message = [];

    /**
     * Validation field description
     * @var array
     */
    protected $field = [];

    /**
     * Default rule hints
     * @var array
     */
    protected static $typeMsg = [
        'require'     => ':attribute require',
        'must'        => ':attribute must',
        'number'      => ':attribute must be numeric',
        'integer'     => ':attribute must be integer',
        'float'       => ':attribute must be float',
        'boolean'     => ':attribute must be bool',
        'email'       => ':attribute not a valid email address',
        'mobile'      => ':attribute not a valid mobile',
        'array'       => ':attribute must be a array',
        'accepted'    => ':attribute must be yes,on or 1',
        'date'        => ':attribute not a valid datetime',
        'file'        => ':attribute not a valid file',
        'alpha'       => ':attribute must be alpha',
        'alphaNum'    => ':attribute must be alpha-numeric',
        'alphaDash'   => ':attribute must be alpha-numeric, dash, underscore',
        'activeUrl'   => ':attribute not a valid domain or ip',
        'chs'         => ':attribute must be chinese',
        'chsAlpha'    => ':attribute must be chinese or alpha',
        'chsAlphaNum' => ':attribute must be chinese,alpha-numeric',
        'chsDash'     => ':attribute must be chinese,alpha-numeric,underscore, dash',
        'url'         => ':attribute not a valid url',
        'ip'          => ':attribute not a valid ip',
        'dateFormat'  => ':attribute must be dateFormat of :rule',
        'in'          => ':attribute must be in :rule',
        'notIn'       => ':attribute be notin :rule',
        'between'     => ':attribute must between :1 - :2',
        'notBetween'  => ':attribute not between :1 - :2',
        'length'      => 'size of :attribute must be :rule',
        'max'         => 'max size of :attribute must be :rule',
        'min'         => 'min size of :attribute must be :rule',
        'after'       => ':attribute cannot be less than :rule',
        'before'      => ':attribute cannot exceed :rule',
        'afterWith'   => ':attribute cannot be less than :rule',
        'beforeWith'  => ':attribute cannot exceed :rule',
        'expire'      => ':attribute not within :rule',
        'allowIp'     => 'access IP is not allowed',
        'denyIp'      => 'access IP denied',
        'confirm'     => ':attribute out of accord with :2',
        'different'   => ':attribute cannot be same with :2',
        'egt'         => ':attribute must greater than or equal :rule',
        'gt'          => ':attribute must greater than :rule',
        'elt'         => ':attribute must less than or equal :rule',
        'lt'          => ':attribute must less than :rule',
        'eq'          => ':attribute must equal :rule',
        'regex'       => ':attribute not conform to the rules',
    ];

    /**
     * Current validation scenario
     * @var array
     */
    protected $currentScene = null;

    /**
     * Filter_var rule
     * @var array
     */
    protected $filter = [
        'email'   => FILTER_VALIDATE_EMAIL,
        'ip'      => [FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6],
        'integer' => FILTER_VALIDATE_INT,
        'url'     => FILTER_VALIDATE_URL,
        'macAddr' => FILTER_VALIDATE_MAC,
        'float'   => FILTER_VALIDATE_FLOAT,
    ];

    /**
     * Built-in regular validation rules
     * @var array
     */
    protected $defaultRegex = [
        'alphaDash'   => '/^[A-Za-z0-9\-\_]+$/',
        'chs'         => '/^[\x{4e00}-\x{9fa5}]+$/u',
        'chsAlpha'    => '/^[\x{4e00}-\x{9fa5}a-zA-Z]+$/u',
        'chsAlphaNum' => '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u',
        'chsDash'     => '/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\_\-]+$/u',
        'mobile'      => '/^1[3-9][0-9]\d{8}$/',
        'idCard'      => '/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}$)/',
        'zip'         => '/\d{6}/',
    ];

    /**
     * Validation scenario definition
     * @var array
     */
    protected $scene = [];

    /**
     * Error message about validation failure
     * @var array
     */
    protected $error = [];

    /**
     * Batch verification
     * @var bool
     */
    protected $batch = false;

    /**
     * Rules that scenarios need to validate
     * @var array
     */
    protected $only = [];

    /**
     * Verification rules to be removed for scenarios
     * @var array
     */
    protected $remove = [];

    /**
     * Scenarios require additional validation rules
     * @var array
     */
    protected $append = [];

    /**
     * Validating regular definitions
     * @var array
     */
    protected $regex = [];

    /**
     * 架构函数
     * @access public
     * @param  array $rules Validation rules
     * @param  array $message Verification prompt
     * @param  array $field Verification field description
     */
    public function __construct(array $rules = [], array $message = [], array $field = [])
    {
        $this->rule    = $rules + $this->rule;
        $this->message = array_merge($this->message, $message);
        $this->field   = array_merge($this->field, $field);
    }

    /**
     * Create a validator class
     * @access public
     * @param  array $rules Validation rules
     * @param  array $message Verification prompt
     * @param  array $field Verification field description
     * @return Validate
     */
    public static function make(array $rules = [], array $message = [], array $field = [])
    {
        return new self($rules, $message, $field);
    }

    /**
     * Add a field verification rule
     * @access protected
     * @param  string|array  $name  Field name or array of rules
     * @param  mixed         $rule  Verification rule or field description
     * @return $this
     */
    public function rule($name, $rule = '')
    {
        if (is_array($name)) {
            $this->rule = $name + $this->rule;
            if (is_array($rule)) {
                $this->field = array_merge($this->field, $rule);
            }
        } else {
            $this->rule[$name] = $rule;
        }

        return $this;
    }

    /**
     * Register extended validation (type) rules
     * @access public
     * @param  string    $type  Validation rule type
     * @param  mixed     $callback
     * @return void
     */
    public static function extend($type, $callback = null)
    {
        if (is_array($type)) {
            self::$type = array_merge(self::$type, $type);
        } else {
            self::$type[$type] = $callback;
        }
    }

    /**
     * Set the default prompt for verification rules
     * @access public
     * @param  string|array  $type  Validation rule type name or array
     * @param  string        $msg  Verification prompt
     * @return void
     */
    public static function setTypeMsg($type, $msg = null)
    {
        if (is_array($type)) {
            self::$typeMsg = array_merge(self::$typeMsg, $type);
        } else {
            self::$typeMsg[$type] = $msg;
        }
    }

    /**
     * Setting prompt message
     * @access public
     * @param  string|array  $name  field name
     * @param  string        $message Prompt information
     * @return Validate
     */
    public function message($name, $message = '')
    {
        if (is_array($name)) {
            $this->message = array_merge($this->message, $name);
        } else {
            $this->message[$name] = $message;
        }

        return $this;
    }

    /**
     * Setting Validation Scenarios
     * @access public
     * @param  string  $name  Scenario name
     * @return $this
     */
    public function scene($name)
    {
        // Setting the current scene
        $this->currentScene = $name;

        return $this;
    }

    /**
     * Determine whether a validation scenario exists
     * @access public
     * @param  string $name Scenario name
     * @return bool
     */
    public function hasScene($name)
    {
        return isset($this->scene[$name]) || method_exists($this, 'scene' . $name);
    }

    /**
     * Setting batch verification
     * @access public
     * @param  bool $batch  Batch verification
     * @return $this
     */
    public function batch($batch = true)
    {
        $this->batch = $batch;

        return $this;
    }

    /**
     * Specify a list of fields to validate
     * @access public
     * @param  array $fields  field name
     * @return $this
     */
    public function only($fields)
    {
        $this->only = $fields;

        return $this;
    }

    /**
     * Removes a validation rule for a field
     * @access public
     * @param  string|array  $field  field name
     * @param  mixed         $rule   Validation rule NULL removes all rules
     * @return $this
     */
    public function remove($field, $rule = null)
    {
        if (is_array($field)) {
            foreach ($field as $key => $rule) {
                if (is_int($key)) {
                    $this->remove($rule);
                } else {
                    $this->remove($key, $rule);
                }
            }
        } else {
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }

            $this->remove[$field] = $rule;
        }

        return $this;
    }

    /**
     * Appends a validation rule for a field
     * @access public
     * @param  string|array  $field  field name
     * @param  mixed         $rule   Validation rules
     * @return $this
     */
    public function append($field, $rule = null)
    {
        if (is_array($field)) {
            foreach ($field as $key => $rule) {
                $this->append($key, $rule);
            }
        } else {
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }

            $this->append[$field] = $rule;
        }

        return $this;
    }

    /**
     * Automatic data validation
     * @access public
     * @param  array     $data
     * @param  mixed     $rules
     * @param  string    $scene
     * @return bool
     */
    public function check($data, $rules = [], $scene = '')
    {
        $this->error = [];

        if (empty($rules)) {
            // Read validation rule
            $rules = $this->rule;
        }

        // Getting the scene definition
        $this->getScene($scene);

        foreach ($this->append as $key => $rule) {
            if (!isset($rules[$key])) {
                $rules[$key] = $rule;
            }
        }

        foreach ($rules as $key => $rule) {
            // field => 'rule1|rule2...' field => ['rule1','rule2',...]
            if (strpos($key, '|')) {
                // Field | description is used to specify the attribute name
                list($key, $title) = explode('|', $key);
            } else {
                $title = isset($this->field[$key]) ? $this->field[$key] : $key;
            }

            // Scenario testing
            if (!empty($this->only) && !in_array($key, $this->only)) {
                continue;
            }

            // Fetching data supports multi-dimensional arrays
            $value = $this->getDataValue($data, $key);

            // Field validation
            if ($rule instanceof \Closure) {
                $result = call_user_func_array($rule, [$value, $data, $title, $this]);
            } else {
                $result = $this->checkItem($key, $value, $rule, $data, $title);
            }

            if (true !== $result) {
                // If no true is returned, validation failed
                if (!empty($this->batch)) {
                    // Batch validation
                    if (is_array($result)) {
                        $this->error = array_merge($this->error, $result);
                    } else {
                        $this->error[$key] = $result;
                    }
                } else {
                    $this->error = $result;
                    return false;
                }
            }
        }

        return !empty($this->error) ? false : true;
    }

    /**
     * Validate data according to validation rules
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rules
     * @return bool
     */
    public function checkRule($value, $rules)
    {
        if ($rules instanceof \Closure) {
            return call_user_func_array($rules, [$value]);
        } elseif (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        foreach ($rules as $key => $rule) {
            if ($rule instanceof \Closure) {
                $result = call_user_func_array($rule, [$value]);
            } else {
                // Determine validation type
                list($type, $rule) = $this->getValidateType($key, $rule);

                $callback = isset(self::$type[$type]) ? self::$type[$type] : [$this, $type];

                $result = call_user_func_array($callback, [$value, $rule]);
            }

            if (true !== $result) {
                return $result;
            }
        }

        return true;
    }

    /**
     * Validate a single field rule
     * @access protected
     * @param  string    $field
     * @param  mixed     $value
     * @param  mixed     $rules
     * @param  array     $data
     * @param  string    $title
     * @param  array     $msg
     * @return mixed
     */
    protected function checkItem($field, $value, $rules, $data, $title = '', $msg = [])
    {
        if (isset($this->remove[$field]) && true === $this->remove[$field] && empty($this->append[$field])) {
            //The field has been removed without verification
            return true;
        }

        // Multi-rule verification is supported require|in:a,b,c|... or ['require','in'=>'a,b,c',...]
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        if (isset($this->append[$field])) {
            // Append additional validation rules
            $rules = array_unique(array_merge($rules, $this->append[$field]), SORT_REGULAR);
        }

        $i      = 0;
        $result = true;

        foreach ($rules as $key => $rule) {
            if ($rule instanceof \Closure) {
                $result = call_user_func_array($rule, [$value, $data]);
                $info   = is_numeric($key) ? '' : $key;
            } else {
                // Determine validation type
                list($type, $rule, $info) = $this->getValidateType($key, $rule);

                if (isset($this->append[$field]) && in_array($info, $this->append[$field])) {

                } elseif (array_key_exists($field, $this->remove) && (null === $this->remove[$field] || in_array($info, $this->remove[$field]))) {
                    // Rule removed
                    $i++;
                    continue;
                }

                // Validation type
                if (isset(self::$type[$type])) {
                    $result = call_user_func_array(self::$type[$type], [$value, $rule, $data, $field, $title]);
                } elseif ('must' == $info || 0 === strpos($info, 'require') || (!is_null($value) && '' !== $value)) {
                    // Validation data
                    $result = call_user_func_array([$this, $type], [$value, $rule, $data, $field, $title]);
                } else {
                    $result = true;
                }
            }

            if (false === $result) {
                // Verification failure returns an error message
                if (!empty($msg[$i])) {
                    $message = $msg[$i];
                    if (is_string($message) && strpos($message, '{%') === 0) {
                        $message = substr($message, 2, -1);
                    }
                } else {
                    $message = $this->getRuleMsg($field, $title, $info, $rule);
                }

                return $message;
            } elseif (true !== $result) {
                // Returns a custom error message
                if (is_string($result) && false !== strpos($result, ':')) {
                    $result = str_replace(':attribute', $title, $result);

                    if (strpos($result, ':rule') && is_scalar($rule)) {
                        $result = str_replace(':rule', (string) $rule, $result);
                    }
                }

                return $result;
            }
            $i++;
        }

        return $result;
    }

    /**
     * Gets the current authentication type and rules
     * @access public
     * @param  mixed     $key
     * @param  mixed     $rule
     * @return array
     */
    protected function getValidateType($key, $rule)
    {
        // Determine validation type
        if (!is_numeric($key)) {
            return [$key, $rule, $key];
        }

        if (strpos($rule, ':')) {
            list($type, $rule) = explode(':', $rule, 2);
            if (isset($this->alias[$type])) {
                // Determine the alias
                $type = $this->alias[$type];
            }
            $info = $type;
        } elseif (method_exists($this, $rule)) {
            $type = $rule;
            $info = $rule;
            $rule = '';
        } else {
            $type = 'is';
            $info = $rule;
        }

        return [$type, $rule, $info];
    }

    /**
     * Verifies that the value matches the value of a field
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @param  array     $data
     * @param  string    $field
     * @return bool
     */
    public function confirm($value, $rule, $data = [], $field = '')
    {
        if ('' == $rule) {
            if (strpos($field, '_confirm')) {
                $rule = strstr($field, '_confirm', true);
            } else {
                $rule = $field . '_confirm';
            }
        }

        return $this->getDataValue($data, $rule) === $value;
    }

    /**
     * Verifies that the value is different from that of a field
     * @access public
     * @param  mixed $value
     * @param  mixed $rule
     * @param  array $data
     * @return bool
     */
    public function different($value, $rule, $data = [])
    {
        return $this->getDataValue($data, $rule) != $value;
    }

    /**
     * Verify that a value is greater than or equal to
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @param  array     $data
     * @return bool
     */
    public function egt($value, $rule, $data = [])
    {
        return $value >= $this->getDataValue($data, $rule);
    }

    /**
     * Verifies that it is greater than a certain value
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @param  array     $data
     * @return bool
     */
    public function gt($value, $rule, $data)
    {
        return $value > $this->getDataValue($data, $rule);
    }

    /**
     * Verifies that it is less than or equal to a value
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @param  array     $data
     * @return bool
     */
    public function elt($value, $rule, $data = [])
    {
        return $value <= $this->getDataValue($data, $rule);
    }

    /**
     * Verifies that it is less than a certain value
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @param  array     $data
     * @return bool
     */
    public function lt($value, $rule, $data = [])
    {
        return $value < $this->getDataValue($data, $rule);
    }

    /**
     * Verify that it is equal to a value
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function eq($value, $rule)
    {
        return $value == $rule;
    }

    /**
     * Must be validated
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function must($value, $rule = null)
    {
        return !empty($value) || '0' == $value;
    }

    /**
     * Verify that the field value is in a valid format
     * @access public
     * @param  mixed     $value
     * @param  string    $rule
     * @param  array     $data
     * @return bool
     */
    public function is($value, $rule, $data = [])
    {
        switch ($rule) {
            case 'require':
                // Must
                $result = !empty($value) || '0' == $value;
                break;
            case 'accepted':
                // Accepted
                $result = in_array($value, ['1', 'on', 'yes']);
                break;
            case 'date':
                // Is an expiration date or not
                $result = false !== strtotime($value);
                break;
            case 'activeUrl':
                // It is a valid url or not
                $result = checkdnsrr($value);
                break;
            case 'boolean':
            case 'bool':
                // Is a Boolean value or not
                $result = in_array($value, [true, false, 0, 1, '0', '1'], true);
                break;
            case 'number':
                $result = ctype_digit((string) $value);
                break;
            case 'alphaNum':
                $result = ctype_alnum($value);
                break;
            case 'array':
                // Is array or not
                $result = is_array($value);
                break;
            default:
                if (isset(self::$type[$rule])) {
                    // Registered validation rules
                    $result = call_user_func_array(self::$type[$rule], [$value]);
                } elseif (function_exists('ctype_' . $rule)) {
                    // Ctype validation rule
                    $ctypeFun = 'ctype_' . $rule;
                    $result   = $ctypeFun($value);
                } elseif (isset($this->filter[$rule])) {
                    // Filter_var validation rule
                    $result = $this->filter($value, $this->filter[$rule]);
                } else {
                    // The regular verification
                    $result = $this->regex($value, $rule);
                }
        }

        return $result;
    }

    /**
     * Verify that the domain name or IP address is valid support A，MX，NS，SOA，PTR，CNAME，AAAA，A6， SRV，NAPTR，TXT or ANY type
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function activeUrl($value, $rule = 'MX')
    {
        if (!in_array($rule, ['A', 'MX', 'NS', 'SOA', 'PTR', 'CNAME', 'AAAA', 'A6', 'SRV', 'NAPTR', 'TXT', 'ANY'])) {
            $rule = 'MX';
        }

        return checkdnsrr($value, $rule);
    }

    /**
     * Verify that the IP address is valid
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule  ipv4 ipv6
     * @return bool
     */
    public function ip($value, $rule = 'ipv4')
    {
        if (!in_array($rule, ['ipv4', 'ipv6'])) {
            $rule = 'ipv4';
        }

        return $this->filter($value, [FILTER_VALIDATE_IP, 'ipv6' == $rule ? FILTER_FLAG_IPV6 : FILTER_FLAG_IPV4]);
    }

    /**
     * Verifies that the time and date conform to the specified format
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function dateFormat($value, $rule)
    {
        $info = date_parse_from_format($rule, $value);
        return 0 == $info['warning_count'] && 0 == $info['error_count'];
    }

    /**
     * Use filter_var for validation
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function filter($value, $rule)
    {
        if (is_string($rule) && strpos($rule, ',')) {
            list($rule, $param) = explode(',', $rule);
        } elseif (is_array($rule)) {
            $param = isset($rule[1]) ? $rule[1] : null;
            $rule  = $rule[0];
        } else {
            $param = null;
        }

        return false !== filter_var($value, is_int($rule) ? $rule : filter_id($rule), $param);
    }

    /**
     * It is mandatory to verify that a field is equal to a value
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @param  array     $data
     * @return bool
     */
    public function requireIf($value, $rule, $data)
    {
        list($field, $val) = explode(',', $rule);

        if ($this->getDataValue($data, $field) == $val) {
            return !empty($value) || '0' == $value;
        }

        return true;
    }

    /**
     * Verify that a field is required through a callback method
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @param  array     $data
     * @return bool
     */
    public function requireCallback($value, $rule, $data)
    {
        $result = call_user_func_array([$this, $rule], [$value, $data]);

        if ($result) {
            return !empty($value) || '0' == $value;
        }

        return true;
    }

    /**
     * Required to verify that a field has a value
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @param  array     $data
     * @return bool
     */
    public function requireWith($value, $rule, $data)
    {
        $val = $this->getDataValue($data, $rule);

        if (!empty($val)) {
            return !empty($value) || '0' == $value;
        }

        return true;
    }

    /**
     * Verify that it is within range
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function in($value, $rule)
    {
        return in_array($value, is_array($rule) ? $rule : explode(',', $rule));
    }

    /**
     * Verify that it is not in a range
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function notIn($value, $rule)
    {
        return !in_array($value, is_array($rule) ? $rule : explode(',', $rule));
    }

    /**
     * between verify data
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function between($value, $rule)
    {
        if (is_string($rule)) {
            $rule = explode(',', $rule);
        }
        list($min, $max) = $rule;

        return $value >= $min && $value <= $max;
    }

    /**
     * use notbetween verify data
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function notBetween($value, $rule)
    {
        if (is_string($rule)) {
            $rule = explode(',', $rule);
        }
        list($min, $max) = $rule;

        return $value < $min || $value > $max;
    }

    /**
     * Validate data length
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function length($value, $rule)
    {
        if (is_array($value)) {
            $length = count($value);
        } else {
            $length = mb_strlen((string) $value);
        }

        if (strpos($rule, ',')) {
            // The length of the interval
            list($min, $max) = explode(',', $rule);
            return $length >= $min && $length <= $max;
        }

        // Specify the length
        return $length == $rule;
    }

    /**
     * Verify the maximum length of data
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function max($value, $rule)
    {
        if (is_array($value)) {
            $length = count($value);
        } else {
            $length = mb_strlen((string) $value);
        }

        return $length <= $rule;
    }

    /**
     * Verify the minimum length of data
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function min($value, $rule)
    {
        if (is_array($value)) {
            $length = count($value);
        } else {
            $length = mb_strlen((string) $value);
        }

        return $length >= $rule;
    }

    /**
     * Verify the date
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @param  array     $data
     * @return bool
     */
    public function after($value, $rule, $data)
    {
        return strtotime($value) >= strtotime($rule);
    }

    /**
     * Verify the date
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @param  array     $data
     * @return bool
     */
    public function before($value, $rule, $data)
    {
        return strtotime($value) <= strtotime($rule);
    }

    /**
     * Validate date field
     * @access protected
     * @param mixed     $value
     * @param mixed     $rule
     * @param array     $data
     * @return bool
     */
    protected function afterWith($value, $rule, $data)
    {
        $rule = $this->getDataValue($data, $rule);
        return !is_null($rule) && strtotime($value) >= strtotime($rule);
    }

    /**
     * Validate date field
     * @access protected
     * @param mixed     $value
     * @param mixed     $rule
     * @param array     $data
     * @return bool
     */
    protected function beforeWith($value, $rule, $data)
    {
        $rule = $this->getDataValue($data, $rule);
        return !is_null($rule) && strtotime($value) <= strtotime($rule);
    }

    /**
     * Validation period
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function expire($value, $rule)
    {
        if (is_string($rule)) {
            $rule = explode(',', $rule);
        }

        list($start, $end) = $rule;

        if (!is_numeric($start)) {
            $start = strtotime($start);
        }

        if (!is_numeric($end)) {
            $end = strtotime($end);
        }

        return $_SERVER['REQUEST_TIME'] >= $start && $_SERVER['REQUEST_TIME'] <= $end;
    }

    /**
     * Verifying IP licenses
     * @access public
     * @param  string    $value
     * @param  mixed     $rule
     * @return mixed
     */
    public function allowIp($value, $rule)
    {
        return in_array($value, is_array($rule) ? $rule : explode(',', $rule));
    }

    /**
     * Verifying IP address disabling
     * @access public
     * @param  string    $value
     * @param  mixed     $rule
     * @return mixed
     */
    public function denyIp($value, $rule)
    {
        return !in_array($value, is_array($rule) ? $rule : explode(',', $rule));
    }

    /**
     * Use re to validate data
     * @access public
     * @param  mixed     $value
     * @param  mixed     $rule
     * @return bool
     */
    public function regex($value, $rule)
    {
        if (isset($this->regex[$rule])) {
            $rule = $this->regex[$rule];
        } elseif (isset($this->defaultRegex[$rule])) {
            $rule = $this->defaultRegex[$rule];
        }

        if (0 !== strpos($rule, '/') && !preg_match('/\/[imsU]{0,4}$/', $rule)) {
            // If it is not a regular expression, fill in both ends/
            $rule = '/^' . $rule . '$/';
        }

        return is_scalar($value) && 1 === preg_match($rule, (string) $value);
    }

    // Get error information
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get data values
     * @access protected
     * @param  array     $data
     * @param  string    $key
     * @return mixed
     */
    protected function getDataValue($data, $key)
    {
        if (is_numeric($key)) {
            $value = $key;
        } elseif (strpos($key, '.')) {
            // Support multi-dimensional array validation
            foreach (explode('.', $key) as $key) {
                if (!isset($data[$key])) {
                    $value = null;
                    break;
                }
                $value = $data = $data[$key];
            }
        } else {
            $value = isset($data[$key]) ? $data[$key] : null;
        }

        return $value;
    }

    /**
     * Obtain an error message about the verification rule
     * @access protected
     * @param  string    $attribute
     * @param  string    $title
     * @param  string    $type
     * @param  mixed     $rule
     * @return string
     */
    protected function getRuleMsg($attribute, $title, $type, $rule)
    {
        if (isset($this->message[$attribute . '.' . $type])) {
            $msg = $this->message[$attribute . '.' . $type];
        } elseif (isset($this->message[$attribute][$type])) {
            $msg = $this->message[$attribute][$type];
        } elseif (isset($this->message[$attribute])) {
            $msg = $this->message[$attribute];
        } elseif (isset(self::$typeMsg[$type])) {
            $msg = self::$typeMsg[$type];
        } elseif (0 === strpos($type, 'require')) {
            $msg = self::$typeMsg['require'];
        } else {
            $msg = $title . ' not conform to the rules';
        }

        if (!is_string($msg)) {
            return $msg;
        }

        if (0 === strpos($msg, '{%')) {
            $msg = substr($msg, 2, -1);
        }

        if (is_scalar($rule) && false !== strpos($msg, ':')) {
            // Variable substitution
            if (is_string($rule) && strpos($rule, ',')) {
                $array = array_pad(explode(',', $rule), 3, '');
            } else {
                $array = array_pad([], 3, '');
            }
            $msg = str_replace(
                [':attribute', ':1', ':2', ':3'],
                [$title, $array[0], $array[1], $array[2]],
                $msg);
            if (strpos($msg, ':rule')) {
                $msg = str_replace(':rule', (string) $rule, $msg);
            }
        }

        return $msg;
    }

    /**
     * Get data validation scenarios
     * @access protected
     * @param  string $scene
     * @return void
     */
    protected function getScene($scene = '')
    {
        if (empty($scene)) {
            // Read the specified scene
            $scene = $this->currentScene;
        }

        $this->only = $this->append = $this->remove = [];

        if (empty($scene)) {
            return;
        }

        if (method_exists($this, 'scene' . $scene)) {
            call_user_func([$this, 'scene' . $scene]);
        } elseif (isset($this->scene[$scene])) {
            // If the verification scenario is specified
            $scene = $this->scene[$scene];

            if (is_string($scene)) {
                $scene = explode(',', $scene);
            }

            $this->only = $scene;
        }
    }

    /**
     * The dynamic method directly calls the is method for validation
     * @access public
     * @param  string $method
     * @param  array $args
     * @return bool
     */
    public function __call($method, $args)
    {
        if ('is' == strtolower(substr($method, 0, 2))) {
            $method = substr($method, 2);
        }

        array_push($args, lcfirst($method));

        return call_user_func_array([$this, 'is'], $args);
    }

    //Used to verify the amount of money when the API requests a transaction
    protected function api_amount($value) {
        return is_scalar($value) && 1 === preg_match('/(^[1-9](\d+)?(\.\d{1,2})?$)|(^0$)|(^\d\.\d{1,2}$)/', (string) $value);
    }

    //Used to verify the url, without the protocol
    protected function check_url($value) {
        if(!is_string($value)) {
            return false;
        }

        $http_pos=strpos($value,'http');
        if ($http_pos!==0) {
            //Add protocol verification
            $value='https://'.$value;
        }

        $tmp_check=parse_url($value);
        if (isset($tmp_check['host'])) {
            return 1 === preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,10}$/', $tmp_check['host']);
        }
        return false;
    }

    //Used to verify the url, and must have a protocol
    protected function check_protocol_url($value)
    {
        if(!is_string($value)) {
            return false;
        }
        if(!filter_var($value, FILTER_VALIDATE_URL))
        {
            return false;
        }
        if (strpos($value,'http://')!==0 && strpos($value,'https://')!==0) {
            return false;
        }
        return true;
    }

    protected function check_card($value)
    {
        $check_value=str_replace(" ","",$value);
        return $this->checkRule($check_value,'number|length:14,20');
    }

    //Used to verify whether the payment mode is a valid IP
    protected function check_ip($value)
    {
        if (strpos($value,':')!==false) {
            return $this->checkRule($value,'ip:ipv6');
        } else {
            return $this->checkRule($value,'ip');
        }
    }
}
