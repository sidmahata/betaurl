<?php
use \AcceptanceTester;
use Page\UrlRESTPage;

class ApitestcestCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function getInvalidUrl(AcceptanceTester $I)
    {
        $I->wantTo('Ensure getting an invalid url id returns a 404 code');
        $I->sendGET(UrlRESTPage::route('23623623.json'));
        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }
    
    public function ensureUrlDefaultResponseTypeIsJson(AcceptanceTester $I)
    {
        $I->wantTo('Ensure dafault response type is json');
        $I->sendGET(UrlRESTPage::route(5));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}
