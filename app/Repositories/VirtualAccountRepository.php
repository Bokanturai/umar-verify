<?php

namespace App\Repositories;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class VirtualAccountRepository
{
    public function createVirtualAccount($loginUserId)
    {
        $userDetails = User::where('id', $loginUserId)->first();

        if (!$userDetails) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Check for required fields for Fintava
        if (empty($userDetails->birthdate)) {
            return ['success' => false, 'message' => 'Please update your profile with your date of birth to create a virtual account.'];
        }

        if (empty($userDetails->first_name) || empty($userDetails->last_name) || empty($userDetails->phone_no) || empty($userDetails->email) || empty($userDetails->address) || empty($userDetails->bvn)) {
            return ['success' => false, 'message' => 'Please complete your profile details (First Name, Last Name, Phone, Email, Address, BVN) to create a virtual account.'];
        }

        try {
            $token = env('FINTAVA_TOKEN');
            $baseUrl = rtrim(env('FINTAVA_BASE_URL'), '/');
            $url = $baseUrl . '/create/customer';

            $data = [
                'firstName' => $userDetails->first_name,
                'lastName' => $userDetails->last_name,
                'phoneNumber' => $userDetails->phone_no,
                'email' => $userDetails->email,
                'fundingMethod' => 'DYNAMIC_FUND',
                'address' => $userDetails->address,
                'dateOfBirth' => $userDetails->birthdate, // Expected format YYYY-MM-DD
                'bvn' => $userDetails->bvn,
                'nin' => $userDetails->nin ?? '77307992925',
            ];

            Log::info('Fintava API Request: ', $data);

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $token, // Assuming Fintava uses Bearer token; adjust if it's a custom header
            ])->post($url, $data);

            Log::info('Fintava API Response: ' . $response->body());

            if (!$response->successful()) {
                $errorData = $response->json();
                $errorMessage = 'Fintava API Error: ' . $response->status();
                $rawMsg = '';

                if (isset($errorData['message'])) {
                    $rawMsg = $errorData['message'];
                } elseif (isset($errorData['errors'])) {
                    $rawMsg = $errorData['errors'];
                }

                if (!empty($rawMsg)) {
                    // Extract first error if it's an array
                    if (is_array($rawMsg)) {
                        $rawMsg = reset($rawMsg);
                    }
                    
                    if (is_string($rawMsg)) {
                        // Clean technical prefixes
                        $errorMessage = preg_replace('/^(TypeORMError|Error):\s*/i', '', $rawMsg);
                    } else {
                        $errorMessage = json_encode($rawMsg);
                    }
                }
                
                throw new Exception($errorMessage);
            }

            $responseData = $response->json();

            // Based on typical Fintava structure (need to verify exact field names from successful response)
            // Assuming successful response contains virtual account details
            if ($response->status() === 201 || (isset($responseData['status']) && ($responseData['status'] === true || $responseData['status'] === 'success' || $responseData['status'] === 200))) {
                
                $data = $responseData['data'] ?? [];
                $walletData = $data['wallet'] ?? [];
                $userInfo = $data['userInfo'] ?? [];

                DB::table('virtual_accounts')->insert([
                    'user_id' => $loginUserId,
                    'accountReference' => $walletData['id'] ?? ($userInfo['id'] ?? 'FNT-' . uniqid()),
                    'accountNo' => $walletData['accountNumber'] ?? 'Pending',
                    'accountName' => $walletData['accountName'] ?? ($userDetails->first_name . ' ' . $userDetails->last_name),
                    'bankName' => 'Loma Bank',
                    'status' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return ['success' => true, 'message' => 'Virtual Account Created Successfully'];
            } else {
                $errorMsg = $responseData['message'] ?? 'Failed to create virtual account';
                if (is_array($errorMsg)) {
                    $errorMsg = json_encode($errorMsg);
                }
                return ['success' => false, 'message' => $errorMsg];
            }

        } catch (\Exception $e) {
            Log::error('Error creating Fintava virtual account for user ' . $loginUserId . ': ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
