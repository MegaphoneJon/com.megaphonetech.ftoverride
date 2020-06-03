# com.megaphonetech.ftoverride

![Screenshot](/images/screenshot.png)

This extension allows donors to set the financial type of a donation while on a contribution page.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v5.4+
* CiviCRM 5.12

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl com.megaphonetech.ftoverride@https://github.com/MegaphoneJon/com.megaphonetech.ftoverride/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone git@github.com:MegaphoneJon/com.megaphonetech.ftoverride.git
cv en com.megaphonetech.ftoverride
```

## Usage

Upon installation, you will find a new field, *Designation*, when creating or editing a contribution page. This you to select financial types that can be associated with your contribution page and selected by a payer when they make their contribution. This new field, *Designation*, will be available in both the back-end user interface and the public-facing contribution page.

![contribution-page-backend.png screenshot](/images/contribution-page-backend.png)

![contribution-page-public.png screenshot](/images/contribution-page-public.png)


## Known Issues

None.
