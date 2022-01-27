# UpCloud WHMCS module

This is a module for adding UpCloud integration onto the WHMCS web hosting platform.

**NOTE:** this module is an experimental one and not officially supported by UpCloud. If you notice anything not working as it should please open a new github -issue. Newest module is available at:

https://github.com/UpCloudLtd/upcloud-whmcs-module/blob/master/upcloud-1.1.12.zip

## Table of content
* [Installation](#installation)
* [Usage](#usage)
* [Documentation](#documentation)
* [Issues](#issues)
* [License](#license)

## Installation

Download the contents of the modules folder into your WHMCS web directory, for example:

```
git clone https://github.com/UpCloudLtd/upcloud-whmcs-module.git ~/upcloud-whmcs-module
```

```
sudo cp -r ~/upcloud-whmcs-module/modules/servers/upCloudVm /var/www/html/whmcs/modules/servers/
```

Then update the permissions to allow the web server user to make changes.

```
sudo chown apache:apache -R /var/www/html/whmcs/modules/servers/upCloudVm/
```

Finally, reload your web server.

```
sudo systemctl reload httpd
```

All set! UpCloud can now be found as an infrastructure provider on your WHMCS admin panel. But before you can begin setting up cloud plans, you'll need to configure API access to your UpCloud account if you haven't already done so.

Follow the instructions at our [API tutorial](https://upcloud.com/community/tutorials/getting-started-upcloud-api/) to configure new API credentials for WHMCS.

## Usage

You should have a working installation of WHMCS together with the UpCloud module to start configuring your services.

To begin, log into your WHMCS Admin Area and find the following section from the navigation bar.

Setup → Products/Services → Servers

First, create a new server configuration that will act as the API control.

In the new server configuration, select UpCloud VM as the server type. Enter necessary API credentials in the following fields for Username and Password. Then test the connection to confirm the details are correct and click the button to Save Changes.

Next, create a new group for the server and assign it to that group.

Continue by navigate to the Products and Services menu.

Setup → Products/Services → Products/Services

Create a new group for your products.

Then, create a new product.

Select the previously created group from the dropdown menu and name your new product.

Next, move to the product Module Settings section and select the module called UpCloud VM as well as the previously created server group.

Then in the product main configuration section, you need to make the following choices:
• Select the Default Location cloud server of this product are deployed to.
• Choose Plan setting the server resources allocated to this product.
• Pick the Template to define the operating system the product will use.

Lastly, decide whether the product requires manual approval from admins or is automatically deployed upon order. Then confirm your selections by clicking the Save Changes button.

Additionally, you may also generate the default Configurable Options and Custom Fields. that can enable you to add extra features such as SSH keys and initialization scripts.

Simply click the corresponding texts in the Module Settings.

With that done, you have your first product now available in your client area.

## Documentation

The above usage instructions are a quick list of step for configuring services and products on WHMCS for UpCloud. You can find more extensible documentation on how to install WHMCS and the UpCloud module at our [community tutorials](https://upcloud.com/community/tutorials/get-started-upcloud-whmcs-module/).

## Issues

What to do if you spot a bug? [Open a new issue here](https://github.com/UpCloudLtd/upcloud-whmcs-module/issues/new).

## License

This project is distributed under the [MIT License](https://opensource.org/licenses/MIT), see LICENSE.txt for more information.
