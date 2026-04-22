<?php

namespace App\Repositories;

use Exception;
use App\Helpers\noncestrHelper;
use App\Helpers\signatureHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VirtualAccountRepository
{

    public function createVirtualAccount(  $loginUserId)
    {

         
            $userDetails = User::where('id', $loginUserId)->first();


            $customer_name = trim($userDetails->first_name . ' ' . $userDetails->last_name);
            
            try {

                $requestTime = (int) (microtime(true) * 1000);
                $noncestr = noncestrHelper::generateNonceStr();
                $accountReference = "F24" . strtoupper(bin2hex(random_bytes(5)));

                $data = [
                    'requestTime' => $requestTime,
                    'identityType' => 'personal',
                    'licenseNumber' =>  $userDetails->bvn,
                    'virtualAccountName' => $customer_name,
                    'version' => env('VERSION'),
                    'customerName' => $customer_name,
                    'email' => $userDetails->email,
                    'accountReference' => $accountReference,
                    'nonceStr' => $noncestr,
                ];

                 Log::info($data); 

                $signature = signatureHelper::generate_signature($data, config('keys.private'));

                $url = env('BASE_URL3') . 'api/v2/virtual/account/label/create';
                $token = env('BEARER_TOKEN');
                $headers = [
                    'Accept: application/json, text/plain, */*',
                    'CountryCode: NG',
                    "Authorization: Bearer $token",
                    "Signature: $signature",
                    'Content-Type: application/json',
                ];

                // Initialize cURL
                $ch = curl_init();

                // Set cURL options
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

                // Execute request
                $response = curl_exec($ch);

                Log::info($response);         
                // Check for cURL errors
                if (curl_errno($ch)) {
                    throw new \Exception('cURL Error: ' . curl_error($ch));
                }

                // Close cURL session
                curl_close($ch);

                // Decode the JSON response to an associative array
                $response = json_decode($response, true);

                // Check if decoding was successful
                if ($response === null) {
                    throw new Exception('Request was not successful.');
                }

                // Check for success
                if (isset($response['respCode']) && $response['respCode'] === '00000000') {

                    $res =  DB::table('virtual_accounts')->insert([
                        'user_id' => $loginUserId,
                        'accountReference' => $response['data']['accountReference'],
                        'accountNo' => $response['data']['virtualAccountNo'],
                        'accountName' => $response['data']['virtualAccountName'],
                        'bankName' => 'PalmPay',
                        'status' => '1',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                      return ['success' => true, 'message' => 'Virtual Account Created'];
                }
            } catch (\Exception $e) {
                Log::error('Error creating virtual account for user ' . $loginUserId . ': ' . $e->getMessage());

                return ['success' => false, 'message' => 'Failed to create virtual account'];
            }
        
    }
}
