<?php
namespace AppBundle;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use UserApp\API;
use UserApp\ServiceException;

class UserApp implements UserAppInterface
{
    private $userId = null;
    private $request;

    function __construct($apiKey, $request)
    {
        $this->request = $request;
        $this->apiKey  = $apiKey;
    }

    public function getUserId()
    {
        if ($this->userId) {
            return $this->userId;
        }

        if (!$this->request->cookies->has('ua_session_token')) {
            return false;
        }

        $ua_session_token = $this->request->cookies->get('ua_session_token');
        //dump($ua_session_token);

        if (isset($ua_session_token)) {  //!User::authenticated() &&
            try {
                $api = new API($this->apiKey, $ua_session_token);
            } catch (ServiceException $exception) {
                // Not authorized
                $valid_token = false;
                throw new AccessDeniedHttpException('Not logged in');
            }
        }

        // Authorized
        $user = $api->user->get();
        if (isset($user[0]->user_id)) {
            $this->userId = $user[0]->user_id;
        }
        if (! $this->userId) {
            throw new AccessDeniedHttpException('Unknown user');
        }
        return $this->userId;
    }}
