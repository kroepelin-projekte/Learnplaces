<?php
$this->add('learnplaceapp/v1/health', 'KPG\Learnplaces\api\App\v1', 'Health@endpoint', 'GET');
$this->add('learnplaceapp/v1/refresh', 'KPG\Learnplaces\api\App\v1', 'Health@refresh', 'GET', true);

$this->add('learnplaceapp/v1/containers', 'KPG\Learnplaces\api\App\v1', 'Containers@endpoint', 'GET', true);

$this->add('learnplaceapp/v1/containers/:container_ref_id', 'KPG\Learnplaces\api\App\v1', 'Learnplaces@endpoint', 'GET', true);

$this->add('learnplaceapp/v1/learnplaces/:id', 'KPG\Learnplaces\api\App\v1', 'LearnplacesInfo@endpoint', 'GET', true);
$this->add('learnplaceapp/v1/learnplaces/:id/checkin', 'KPG\Learnplaces\api\App\v1', 'CheckinByGeolocation@endpoint', 'POST', true);
$this->add('learnplaceapp/v1/learnplaces/verifyqrcode/:token', 'KPG\Learnplaces\api\App\v1', 'VerifyQRCode@endpoint', 'POST', true);
$this->add('learnplaceapp/v1/resources/:rid', 'KPG\Learnplaces\api\App\v1', 'GetResources@endpoint', 'GET', true);

$this->add('learnplaceapp/v1/maps-tour', 'KPG\Learnplaces\api\App\v1', 'MapsTourOverview@endpoint', 'GET', true);
$this->add('learnplaceapp/v1/maps-tour/:id', 'KPG\Learnplaces\api\App\v1', 'MapsTour@endpoint', 'GET', true);
$this->add('learnplaceapp/v1/maps-collection', 'KPG\Learnplaces\api\App\v1', 'MapsCollectionOverview@endpoint', 'GET', true);
$this->add('learnplaceapp/v1/maps-collection/:id', 'KPG\Learnplaces\api\App\v1', 'MapsCollection@endpoint', 'GET', true);

$this->add('learnplaceapp/v1/auth', 'KPG\Learnplaces\api\App\v1', 'Auth@endpoint', 'GET');
$this->add('learnplaceapp/v1/token', 'KPG\Learnplaces\api\App\v1', 'Token@endpoint', 'POST');
