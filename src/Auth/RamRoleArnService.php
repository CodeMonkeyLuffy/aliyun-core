<?php

namespace CodeMonkeyLuffy\Aliyun\Core\Auth;

use CodeMonkeyLuffy\Aliyun\Core\Http\HttpHelper;
use CodeMonkeyLuffy\Aliyun\Core\RpcAcsRequest;

define('STS_PRODUCT_NAME', 'Sts');

define('STS_DOMAIN', 'sts.aliyuncs.com');

define('STS_VERSION', '2015-04-01');

define('STS_ACTION', 'AssumeRole');

define('STS_REGION', 'cn-hangzhou');

define('ROLE_ARN_EXPIRE_TIME', 3600);

class AssumeRoleRequest extends RpcAcsRequest
{
    /**
     * AssumeRoleRequest constructor.
     *
     * @param $roleArn
     * @param $roleSessionName
     */
    public function __construct($roleArn, $roleSessionName)
    {
        parent::__construct(STS_PRODUCT_NAME, STS_VERSION, STS_ACTION);

        $this->queryParameters['RoleArn'] = $roleArn;
        $this->queryParameters['RoleSessionName'] = $roleSessionName;
        $this->queryParameters['DurationSeconds'] = ROLE_ARN_EXPIRE_TIME;
        $this->setRegionId(ROLE_ARN_EXPIRE_TIME);
        $this->setProtocol('https');

        $this->setAcceptFormat('JSON');
    }
}

class RamRoleArnService
{
    /**
     * @var IClientProfile
     */
    private $clientProfile;
    /**
     * @var null|string
     */
    private $lastClearTime = null;
    /**
     * @var null|string
     */
    private $sessionCredential = null;
    /**
     * @var string
     */
    public static $serviceDomain = STS_DOMAIN;

    /**
     * RamRoleArnService constructor.
     *
     * @param $clientProfile
     */
    public function __construct($clientProfile)
    {
        $this->clientProfile = $clientProfile;
    }

    /**
     * @return Credential|string|null
     * @throws ClientException
     */
    public function getSessionCredential()
    {
        if ($this->lastClearTime != null && $this->sessionCredential != null) {
            $now = time();
            $elapsedTime = $now - $this->lastClearTime;
            if ($elapsedTime <= ROLE_ARN_EXPIRE_TIME * 0.8) {
                return $this->sessionCredential;
            }
        }

        $credential = $this->assumeRole();

        if ($credential == null) {
            return null;
        }

        $this->sessionCredential = $credential;
        $this->lastClearTime = time();

        return $credential;
    }

    /**
     * @return Credential|null
     * @throws ClientException
     */
    private function assumeRole()
    {
        $signer = $this->clientProfile->getSigner();
        $ramRoleArnCredential = $this->clientProfile->getCredential();

        $request =
            new AssumeRoleRequest($ramRoleArnCredential->getRoleArn(), $ramRoleArnCredential->getRoleSessionName());

        $requestUrl = $request->composeUrl($signer, $ramRoleArnCredential, self::$serviceDomain);

        $httpResponse = HttpHelper::curl($requestUrl, $request->getMethod(), null, $request->getHeaders());

        if (!$httpResponse->isSuccess()) {
            return null;
        }

        $respObj = json_decode($httpResponse->getBody());

        $sessionAccessKeyId = $respObj->Credentials->AccessKeyId;
        $sessionAccessKeySecret = $respObj->Credentials->AccessKeySecret;
        $securityToken = $respObj->Credentials->SecurityToken;
        return new Credential($sessionAccessKeyId, $sessionAccessKeySecret, $securityToken);
    }
}
