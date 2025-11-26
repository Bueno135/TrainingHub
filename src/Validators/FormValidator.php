<?php
// ============================================
// src/Validators/FormValidator.php
// ============================================
class FormValidator {
    private $errors = [];

    public function validate($data, $rules) {
        $this->errors = [];

        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            $ruleArray = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;

            foreach ($ruleArray as $rule) {
                $this->applyRule($field, $value, $rule, $data);
            }
        }

        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    public function getError($field) {
        return $this->errors[$field] ?? null;
    }

    private function applyRule($field, $value, $rule, $allData) {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $param] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $param = null;
        }

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->errors[$field] = "O campo {$field} é obrigatório";
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = "O campo {$field} deve ser um email válido";
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < intval($param)) {
                    $this->errors[$field] = "O campo {$field} deve ter no mínimo {$param} caracteres";
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > intval($param)) {
                    $this->errors[$field] = "O campo {$field} deve ter no máximo {$param} caracteres";
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->errors[$field] = "O campo {$field} deve ser numérico";
                }
                break;

            case 'date':
                if (!empty($value)) {
                    $d = DateTime::createFromFormat('Y-m-d', $value);
                    if (!$d || $d->format('Y-m-d') !== $value) {
                        $this->errors[$field] = "O campo {$field} deve ser uma data válida (YYYY-MM-DD)";
                    }
                }
                break;

            case 'in':
                if (!empty($value)) {
                    $allowed = explode(',', $param);
                    if (!in_array($value, $allowed)) {
                        $this->errors[$field] = "O campo {$field} deve ser um dos valores: " . implode(', ', $allowed);
                    }
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if (!isset($allData[$confirmField]) || $value !== $allData[$confirmField]) {
                    $this->errors[$field] = "O campo {$field} não confere com a confirmação";
                }
                break;

            case 'cpf':
                if (!empty($value) && !$this->validateCPF($value)) {
                    $this->errors[$field] = "O campo {$field} deve ser um CPF válido";
                }
                break;

            case 'cnpj':
                if (!empty($value) && !$this->validateCNPJ($value)) {
                    $this->errors[$field] = "O campo {$field} deve ser um CNPJ válido";
                }
                break;
        }
    }

    private function validateCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    private function validateCNPJ($cnpj) {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        if (strlen($cnpj) != 14) {
            return false;
        }

        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        $length = 12;
        $digits = substr($cnpj, 0, $length);
        $sum = 0;
        $pos = $length - 7;

        for ($i = 0; $i < $length; $i++) {
            $sum += $digits[$i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }

        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
        if ($result != $cnpj[12]) {
            return false;
        }

        $length = 13;
        $digits = substr($cnpj, 0, $length);
        $sum = 0;
        $pos = $length - 7;

        for ($i = 0; $i < $length; $i++) {
            $sum += $digits[$i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }

        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
        return $result == $cnpj[13];
    }
}

