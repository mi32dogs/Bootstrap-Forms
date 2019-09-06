<?php

namespace ShowPro\BootstrapForms;

use SilverStripe\Core\Extension;

class BootstrapUserForm extends Extension
{


    public function updateForm($form)
    {
        $form->Fields()->bootstrapify();
        $form->Actions()->bootstrapify();
        $form->setTemplate("BootstrapForm");
    }

}
