# PoC - End to End Encryption

## Install ##

`composer install`

## Start server ##

`php -S localhost:1234 -t ./public`

## Reset database ##

Delete folders in /storage/microdb

## Demo Scenario ##

### Step 1 ###

Create an admin:

* navigate to http://localhost:1234/admin
* provide an email address and a password in section `Create New Admin`
* click `Save`
* newly created admin ID is displayed in section `Load admin` in field `Admin ID`
* remeber the admin password

### Step 2 ###

Create a new data request.

* navigate to http://localhost:1234/client
* fill in the form in section `Create New Datarequest`
* click `Encrypt`
* click `Save`
* the newly created request ID is shown in the `Load Request` section
* remember the client password
* remember the request ID

### Step 3 ###

View the request with the admin and send back a(n encrypted) message to the client

* navigate to http://localhost:1234/admin
* provide the admin ID in section `Load Admin`
* click `Load admin` - if ID is correct, the admin data is loaded
* provide the data request ID in section `Load Request`
* click `Load` - if ID is correct, encrypted datarequest is loaded
* provide the admin password in section `Load Request` in field `Decrypt with password`
* click `Decrypt` - if password is correct, data is decrypted
* type in a message in `Admin message`
* click `Update` - message is encrypted and saved back to datarequest

### Step 4 ###

Read the admin message with the client

* navigate to http://localhost:1234/client
* provide datarequest ID in section `Load Request`
* click `Load` - if ID is correct encrypted data is loaded
* provide client password in section `Load Request` in field `Decrypt with password`
* click `Decrypt` - if client password is correct, data is decrypted
