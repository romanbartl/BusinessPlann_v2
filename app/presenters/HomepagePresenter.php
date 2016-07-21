<?php

namespace App\Presenters;

use Nette;
use App\Model;


class HomepagePresenter extends BasePresenter
{
    public function actionRedirect()
    {
        $this->redirect('Homepage:default', array('locale' => 'cs'));
    }

}
