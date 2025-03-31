<?php

$this->add('/learnplaceapp/v1/login', 'KPG\Learnplaces\api\App\v1', 'Login@endpoint', 'POST', "basic_auth");
$this->add('/learnplaceapp/v1/learnplaces', 'KPG\Learnplaces\api\App\v1', 'Learnplaces@endpoint', 'GET', 'token_auth');
$this->add('/learnplaceapp/v1/learnplaces/:id', 'KPG\Learnplaces\api\App\v1', 'LearnplacesInfo@endpoint', 'GET', 'token_auth');
$this->add('/learnplaceapp/v1/learnplaces/:id/verify/:token', 'KPG\Learnplaces\api\App\v1', 'VerifyQRCode@endpoint', 'POST', 'token_auth');
$this->add('/learnplaceapp/v1/resources/:rid', 'KPG\Learnplaces\api\App\v1', 'GetResources@endpoint', 'GET', 'token_auth');

// todo auth mode nicht protected
$this->add('/learnplaceapp/v1/auth', 'KPG\Learnplaces\api\App\v1', 'Auth@endpoint', 'GET', 'token_auth');
$this->add('/learnplaceapp/v1/token', 'KPG\Learnplaces\api\App\v1', 'Token@endpoint', 'POST', 'token_auth');
