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

$files = glob(dirname(dirname(__FILE__)).'/storage/target.config.d/*');
foreach ($files as $file)
 {
    if (is_file($file))
    {
        unlink($file);
    }
}
