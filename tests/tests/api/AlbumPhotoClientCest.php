<?php
use ApiGuyTester;


class AlbumPhotoClientCest
{
   public $photoToken = '3fEwuRzczeguZNny4T9Z2LG_1feu0S-A';
   public $clientToken = 'kKdzaUE10lMa13EqgC1uRGgNYmeuQJt2';

    public function _before(ApiGuyTester $I)
    {
  
    }

    public function _after(ApiGuyTester $I)
    {
 
    }

    // tests
    public function tryToTestCreatePhoto(ApiGuyTester $I, $scenario)
    {
       $I = new ApiGuyTester($scenario);
       $I->wantTo('create a album via API I am photograper');
       $I->amBearerAuthenticated($this->photoToken);
       $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
       $I->sendPOST('albums', ['name' => 'Holiday']);
       $I->seeResponseCodeIs(201);
       $I->seeResponseIsJson();
       $I->seeResponseContainsJson(['name' => 'Holiday']);
       $I->seeInDatabase('albums', ['name' => 'Holiday']);
       
       $I->sendPOST('albums', ['name' => 'Wedding']);
       $I->seeResponseCodeIs(201);
       $I->seeResponseIsJson();
       $I->seeResponseContainsJson(['name' => 'Wedding']);
       $I->seeInDatabase('albums', ['name' => 'Wedding']);
       $idClient = $I->grabFromDatabase('users', 'id',  ['access_token' => $this->clientToken]);
       $idAlbum =  $I->grabFromDatabase('albums', 'id',  ['name' => 'Wedding']);
       $I->sendPOST('album-clients', ['album_id' => $idAlbum, 'user_id'=>$idClient]);
       $I->seeResponseCodeIs(201);

    }
    
    public function tryToTestCreateClient(ApiGuyTester $I, $scenario)
    {
       $I = new ApiGuyTester($scenario);
       $I->wantTo('create a user via API I am client');
       $I->amBearerAuthenticated($this->clientToken);
       $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
       $I->sendPOST('albums', ['name' => 'Holiday']);
       $I->seeResponseCodeIs(403); 
    }


    public function tryToTestIndexPhoto(ApiGuyTester $I, $scenario)
    
    {
       $I->wantTo('index the albums via API I am photograper');
       $I->amBearerAuthenticated($this->photoToken);
       $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
       $I->sendGet('albums');
       $I->seeResponseCodeIs(200);
       $I->seeResponseIsJson();
     }  

    public function tryToTestIndexClient(ApiGuyTester $I, $scenario)
    
    {
       $I->wantTo('index the albums via API I am client');
       $I->amBearerAuthenticated($this->clientToken);
       $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
       $I->sendGet('albums');
       $I->seeResponseCodeIs(200);
       $I->seeResponseIsJson();
     }  

     
    public function tryToTestViewPhoto(ApiGuyTester $I, $scenario)
    {
       $id = $I->grabFromDatabase('albums', 'id', ['name' => 'Holiday']);
       $I->wantTo('view the album via API I am photographer');
       $I->amBearerAuthenticated($this->photoToken);
       $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
       $I->sendGet('albums'.'/'.$id);
       $I->seeResponseCodeIs(200);
       $I->seeResponseIsJson();
       $I->seeResponseContainsJson(['id' => (int) $id]);
     }  
    
    public function tryToTestViewClient(ApiGuyTester $I, $scenario)
    {
      $id =  $I->grabFromDatabase('albums', 'id',  ['name' => 'Wedding']);
       $I->wantTo('view the album via API I am client');
       $I->amBearerAuthenticated($this->clientToken);
       $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
       $I->sendGet('albums'.'/'.$id);
       $I->seeResponseCodeIs(200);
       $I->seeResponseIsJson();
       $I->seeResponseContainsJson(['id' => (int) $id]);
       $id = $I->grabFromDatabase('albums', 'id',  ['name' => 'Holiday']);
       $I->sendGet('albums'.'/'.$id);
       $I->seeResponseCodeIs(403);

     }  

    public function tryToUpdatePhoto(ApiGuyTester $I, $scenario)
    {
       $id = $I->grabFromDatabase('albums', 'id', ['name' => 'Holiday']);
       $I->wantTo('update the album via API I am photographer');
       $I->amBearerAuthenticated($this->photoToken);
       $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
       $I->sendPut('albums'.'/'.$id, ['name' => 'Fifties']);
       $I->seeResponseCodeIs(200);
       $I->seeResponseIsJson();
       $I->seeResponseContainsJson(['name' => 'Fifties']);
       $I->seeInDatabase('albums', ['name' => 'Fifties']);
     }

    public function tryToUpdateClient(ApiGuyTester $I, $scenario)
    {
       $id = $I->grabFromDatabase('albums', 'id', ['name' => 'Wedding']);
       $I->wantTo('update the album via API I am client');
       $I->amBearerAuthenticated($this->clientToken);
       $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
       $I->sendPut('albums'.'/'.$id, ['name' => 'Fifties']);
       $I->seeResponseCodeIs(403);
      
     }

    public function tryToTestDeletePhoto(ApiGuyTester $I, $scenario)
    {
       $id = $I->grabFromDatabase('albums', 'id',  ['name' => 'Fifties']);
       $I->wantTo('delete the albums via API I am admin');
       $I->amBearerAuthenticated($this->photoToken);
       $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
       $I->sendDelete('albums'.'/'.$id);
       $I->seeResponseCodeIs(403);
       
     }  

  public function tryToTestDeleteClient(ApiGuyTester $I, $scenario)
    {
       $id = $I->grabFromDatabase('albums', 'id',  ['name' => 'Wedding']);
       $I->wantTo('delete the albums via API I am admin');
       $I->amBearerAuthenticated($this->clientToken);
       $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
       $I->sendDelete('albums'.'/'.$id);
       $I->seeResponseCodeIs(403);
       
     }        

}
