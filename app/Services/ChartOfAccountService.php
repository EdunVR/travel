<?php

namespace App\Services;

class ChartOfAccountService
{
    protected $accounts;

    public function __construct()
    {
        $this->accounts = config('accounts.accounts');
    }

    /**
     * Get all accounts in a flat array
     */
    public function getAllAccounts($parentCode = null, $level = 0)
    {
        $result = [];
        
        foreach ($this->accounts as $account) {
            $this->flattenAccount($account, $result, $parentCode, $level);
        }
        
        return $result;
    }

    /**
     * Flatten account hierarchy
     */
    protected function flattenAccount($account, &$result, $parentCode = null, $level = 0, $parentLevel = 0)
    {
        $account['level'] = $level;
        $account['parent_code'] = $parentCode;
        
        if ($parentCode === null || strpos($account['code'], $parentCode) === 0) {
            $result[] = $account;
            
            if (!empty($account['children'])) {
                foreach ($account['children'] as $child) {
                    $this->flattenAccount($child, $result, $account['code'], $level + 1);
                }
            }
        }
    }

    /**
     * Get account by code
     */
    public function getAccountByCode($code)
    {
        foreach ($this->accounts as $account) {
            $found = $this->findAccountByCode($account, $code);
            if ($found) {
                //return $found;
                return [
                    'code' => $found['code'] ?? null,
                    'name' => $found['name'] ?? null,
                    'type' => $found['type'] ?? 'asset',
                    'is_active' => $found['is_active'] ?? true,
                    'children' => $found['children'] ?? []
                ];
            }
        }
        return null;
    }

    protected function findAccountByCode($account, $code)
    {
        if ($account['code'] === $code) {
            return $account;
        }
        
        if (!empty($account['children'])) {
            foreach ($account['children'] as $child) {
                $found = $this->findAccountByCode($child, $code);
                if ($found) {
                    return $found;
                }
            }
        }
        
        return null;
    }

    /**
     * Get accounts by type
     */
    public function getAccountsByType($type)
    {
        $result = [];
        
        foreach ($this->accounts as $account) {
            if ($account['type'] === $type) {
                $result[] = $account;
            }
            
            if (!empty($account['children'])) {
                $this->findAccountsByType($account['children'], $type, $result);
            }
        }
        
        return $result;
    }

    protected function findAccountsByType($accounts, $type, &$result)
    {
        foreach ($accounts as $account) {
            if ($account['type'] === $type) {
                $result[] = $account;
            }
            
            if (!empty($account['children'])) {
                $this->findAccountsByType($account['children'], $type, $result);
            }
        }
    }

    /**
     * Get account hierarchy tree
     */
    public function getAccountTree()
    {
        return $this->accounts;
    }

    /**
     * Get parent accounts
     */
    public function getParentAccounts()
    {
        $result = [];
        
        foreach ($this->accounts as $account) {
            $result[] = [
                'code' => $account['code'],
                'name' => $account['name'],
                'type' => $account['type']
            ];
            
            if (!empty($account['children'])) {
                $this->findParentAccounts($account['children'], $result);
            }
        }
        
        return $result;
    }

    protected function findParentAccounts($accounts, &$result)
    {
        foreach ($accounts as $account) {
            if (!empty($account['children'])) {
                $result[] = [
                    'code' => $account['code'],
                    'name' => $account['name'],
                    'type' => $account['type']
                ];
                $this->findParentAccounts($account['children'], $result);
            }
        }
    }

    public function getDefaultPaymentAccount()
    {
        // Cek dari config, fallback ke default
        $accountCode = config('accounts.default_payment_account', '1.01.01.01');
        
        // Verifikasi akun ada
        if (!$this->getAccountByCode($accountCode)) {
            throw new \Exception("Default payment account {$accountCode} not found in COA");
        }
        
        return $accountCode;
    }

    public function getCashAccounts()
    {
        $cashParentCode = '1.01.01';
        logger()->debug('Searching for cash accounts in COA structure', [
            'full_structure' => $this->accounts
        ]);
        $cashAccounts = [];
        
        // Fungsi rekursif untuk mencari akun kas
        $findCashAccounts = function ($accounts, $parentCode) use (&$findCashAccounts) {
            foreach ($accounts as $account) {
                if ($account['code'] === $parentCode && isset($account['children'])) {
                    return $account['children'];
                }
                
                if (!empty($account['children'])) {
                    $found = $findCashAccounts($account['children'], $parentCode);
                    if ($found) {
                        return $found;
                    }
                }
            }
            return null;
        };
        
        $cashAccounts = $findCashAccounts($this->accounts, $cashParentCode);
        
        if (empty($cashAccounts)) {
            logger()->error('Cash accounts not found under parent code', [
                'parent_code' => $cashParentCode,
                'available_accounts' => $this->accounts
            ]);
            throw new \Exception("Cash accounts configuration not found under parent code {$cashParentCode}");
        }
        
        return $cashAccounts;
    }

    public function generateAccountCode($parentCode = null)
    {
        $accounts = $this->accounts;
        
        if ($parentCode) {
            // Cari parent account
            $parentAccount = $this->findAccountByCodeRecursive($accounts, $parentCode);
            
            if (!$parentAccount) {
                throw new \Exception("Parent account tidak ditemukan");
            }
            
            // Generate kode untuk child
            if (empty($parentAccount['children'])) {
                return $parentCode . '.01'; // Child pertama
            }
            
            // Ambil child terakhir
            $lastChild = end($parentAccount['children']);
            $lastCode = $lastChild['code'];
            
            // Ekstrak angka terakhir
            $parts = explode('.', $lastCode);
            $lastNumber = (int)end($parts);
            
            // Generate angka berikutnya dengan leading zero
            $nextNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
            
            return $parentCode . '.' . $nextNumber;
        } else {
            // Generate kode untuk parent (level 1)
            $lastParent = end($accounts);
            $lastCode = $lastParent['code'];
            $nextNumber = (int)$lastCode + 1;
            
            return (string)$nextNumber;
        }
    }

    protected function findAccountByCodeRecursive($accounts, $code)
    {
        foreach ($accounts as $account) {
            if ($account['code'] === $code) {
                return $account;
            }
            
            if (!empty($account['children'])) {
                $found = $this->findAccountByCodeRecursive($account['children'], $code);
                if ($found) {
                    return $found;
                }
            }
        }
        
        return null;
    }

    public function getAccountsByCodeLike($pattern)
    {
        $result = [];
        $allAccounts = $this->getAllAccounts();
        
        foreach ($allAccounts as $account) {
            if (strpos($account['code'], $pattern) === 0) {
                $result[] = $account;
            }
        }
        
        return $result;
    }

    public function getAccountsByParentCode($parentCode)
    {
        $allAccounts = $this->getAllAccounts();
        $filteredAccounts = [];
        
        foreach ($allAccounts as $account) {
            if (strpos($account['code'], $parentCode) === 0 && $account['code'] !== $parentCode) {
                $filteredAccounts[] = $account;
            }
        }
        
        return $filteredAccounts;
    }

    public function getAccountsByPrefix($prefix)
    {
        $allAccounts = $this->getAllAccounts();
        $filteredAccounts = [];
        
        foreach ($allAccounts as $account) {
            if (strpos($account['code'], $prefix) === 0) {
                $filteredAccounts[] = $account;
            }
        }
        
        // Urutkan berdasarkan kode akun
        usort($filteredAccounts, function($a, $b) {
            return strcmp($a['code'], $b['code']);
        });
        
        return $filteredAccounts;
    }
}