<?php

/**
 * Created by ModulesGarden.
 *
 * PHP version 7
 *
 * @author ModulesGarden <contact@modulesgarden.com>
 * @link https://www.modulesgarden.com/
 *
 *  * ******************************************************************
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *  * ******************************************************************
 */

use ModulesGarden\upCloudVm\Helper;

add_hook('AdminProductConfigFields', 1, function () {
    if (!empty(App::getFromRequest("generateFields")))
    {
        Helper::generateFields(App::getFromRequest("generateFields"));
    }
});

add_hook('ClientAreaFooterOutput', 1, function () {
    return '<style>#additionalinfo .text-left{ word-break: break-all;}</style>';
});
