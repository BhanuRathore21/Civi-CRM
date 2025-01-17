<?php

/*
 * This file is part of the AuthnetJSON package.
 *
 * (c) John Conde <stymiee@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*************************************************************************************************

Use the AIM JSON API to process an Authorize and Capture transaction through Paypal

SAMPLE REQUEST
--------------------------------------------------------------------------------------------------
{
    "createTransactionRequest": {
        "merchantAuthentication": {
            "name": "cnpdev4289",
            "transactionKey": "SR2P8g4jdEn7vFLQ"
        },
        "transactionRequest": {
            "transactionType": "authCaptureTransaction",
            "amount": "80.93",
            "payment": {
                "payPal": {
                    "successUrl": "https://my.server.com/success.html",
                    "cancelUrl": "https://my.server.com/cancel.html",
                    "paypalLc": "",
                    "paypalHdrImg": "",
                    "paypalPayflowcolor": "FFFF00"
                }
            },
            "lineItems": {
                "lineItem": {
                    "itemId": "item1",
                    "name": "golf balls",
                    "quantity": "1",
                    "unitPrice": "18.95"
                }
            }
        }
    }
}

SAMPLE RESPONSE
--------------------------------------------------------------------------------------------------
{
    "transactionResponse": {
        "responseCode": "1",
        "transId": "2149186848",
        "refTransID": "2149186775",
        "transHash": "D6C9036F443BADE785D57DA2B44CD190",
        "testRequest": "0",
        "accountType": "PayPal",
        "messages": [
            {
                "code": "1",
                "description": "This transaction has been approved."
            }
        ]
    },
    "refId": "123456",
    "messages": {
        "resultCode": "Ok",
        "message": [
            {
                "code": "I00001",
                "text": "Successful."
            }
        ]
    }
}

*************************************************************************************************/

    namespace JohnConde\Authnet;

    require('../../config.inc.php');
    require('../../src/autoload.php');

    $request  = AuthnetApiFactory::getJsonApiHandler(AUTHNET_LOGIN, AUTHNET_TRANSKEY, AuthnetApiFactory::USE_DEVELOPMENT_SERVER);
    $response = $request->createTransactionRequest([
        "transactionRequest" => [
            "transactionType" => "authCaptureTransaction",
            "amount" => "80.93",
            "payment" => [
                "payPal" => [
                    "successUrl" => "https://my.server.com/success.html",
                    "cancelUrl" => "https://my.server.com/cancel.html",
                    "paypalLc" => "",
                    "paypalHdrImg" => "",
                    "paypalPayflowcolor" => "FFFF00"
                ]
            ],
            "lineItems" => [
                "lineItem" => [
                    "itemId" => "item1",
                    "name" => "golf balls",
                    "quantity" => "1",
                    "unitPrice" => "18.95"
                ]
            ]
        ]
    ]);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AIM :: Paypal :: Authorize and Capture</title>
        <style type="text/css">
            table
            {
                border: 1px solid #cccccc;
                margin: auto;
                border-collapse: collapse;
                max-width: 90%;
            }

            table td
            {
                padding: 3px 5px;
                vertical-align: top;
                border-top: 1px solid #cccccc;
            }

            pre
            {
            	overflow-x: auto; /* Use horizontal scroller if needed; for Firefox 2, not needed in Firefox 3 */
            	white-space: pre-wrap; /* css-3 */
            	white-space: -moz-pre-wrap !important; /* Mozilla, since 1999 */
            	white-space: -pre-wrap; /* Opera 4-6 */
            	white-space: -o-pre-wrap; /* Opera 7 */ /*
            	width: 99%; */
            	word-wrap: break-word; /* Internet Explorer 5.5+ */
            }

            table th
            {
                background: #e5e5e5;
                color: #666666;
            }

            h1, h2
            {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <h1>
            AIM :: Authorize and Capture
        </h1>
        <h2>
            Results
        </h2>
        <table>
            <tr>
                <th>Response</th>
                <td><?php echo $response->messages->resultCode; ?></td>
            </tr>
            <tr>
                <th>Successful?</th>
                <td><?php echo ($response->isSuccessful()) ? 'yes' : 'no'; ?></td>
            </tr>
            <tr>
                <th>Error?</th>
                <td><?php echo ($response->isError()) ? 'yes' : 'no'; ?></td>
            </tr>
            <?php if ($response->isSuccessful()) : ?>
            <tr>
                <th>Description</th>
                <td><?php echo $response->transactionResponse->messages[0]->description; ?></td>
            </tr>
            <tr>
                <th>authCode</th>
                <td><?php echo $response->transactionResponse->authCode; ?></td>
            </tr>
            <tr>
                <th>transId</th>
                <td><?php echo $response->transactionResponse->transId; ?></td>
            </tr>
            <?php elseif ($response->isError()) : ?>
            <tr>
                <th>Error Code</th>
                <td><?php echo $response->getErrorCode(); ?></td>
            </tr>
            <tr>
                <th>Error Message</th>
                <td><?php echo  $response->getErrorText(); ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <h2>
            Raw Input/Output
        </h2>
<?php
    echo $request, $response;
?>
    </body>
</html>
