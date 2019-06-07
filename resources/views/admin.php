<!DOCTYPE html>
<html>
<head>
    <script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
    <!-- https://github.com/wwwtyro/cryptico -->
    <script src="/cryptico.js"></script>
    <script>
        var bits = 1024;
    </script>
</head>
<body>
    <h1>CREATE NEW ADMIN</h1>
    Email: <input id="email" type="text" name="email" /><br />
    Password: <input id="password" type="text" name="password" /><br />
    <br />
    <button id="btn-submit">Save</button>
    <hr />
    <h1>LOAD ADMIN</h1>
    Admin ID: <input id="admin-id" type="text" name="admin-id" /><br />
    Email: <input id="admin-email" type="text" name="admin-email" disabled /><br />
    Public key string: <input id="public-key-string" type="text" name="public-key-string" disabled /><br />
    <br />
    <button id="btn-load-admin">Load admin</button>
    <hr />
    <h1>LOAD REQUEST</h1>
    Request ID: <input id="request-id" type="text" name="requet-id" /><br />
    Decrypt with password: <input id="decrypt-password" type="text" name="decrypt-password" /><br />
    <br />
    <button id="btn-load">Load</button>
    <button id="btn-decrypt">Decrypt</button>
    <hr />
    Shared secret: <input id="shared-secret" type="text" name="shared-secret" disabled /><br />
    Email: <input id="client-email" type="text" name="client-email" disabled /><br />
    Data 1: <input id="data-1" type="text" name="data-1" disabled /><br />
    Data 2: <input id="data-2" type="text" name="data-2" disabled /><br />
    Data 3: <input id="data-3" type="text" name="data-3" disabled /><br />
    Data 4: <input id="data-4" type="text" name="data-4" disabled /><br />
    Admin message: <input id="admin-message" type="text" name="admin-message" /><br />
    <br />
    <button id="btn-update">Update</button>
    <script>
        $('#btn-submit').on('click', function(){
            var email = $("#email").val();
            var password = $("#password").val();
            var adminRsaKey = cryptico.generateRSAKey(password, bits);
            var adminPublicKeyString = cryptico.publicKeyString(adminRsaKey);
            var request = {
                email: email,
                publicKeyString: adminPublicKeyString
            }
            $.ajax({
                type: "POST",
                url: "/api/admin",
                data: JSON.stringify(request),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    $("#email").val("");
                    $("#password").val("");
                    $("#admin-id").val(data.id);
                },
                failure: function(err) {
                    console.err(err);
                }
            });
        });

        $('#btn-load-admin').on('click', function(){
            var adminId = $("#admin-id").val();
            $.ajax({
                type: "GET",
                url: "/api/admin/" + adminId,
                data: null,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    var adminId = $("#admin-id").val();
                    $("#admin-email").val(data.email ? data.email : "");
                    $("#public-key-string").val(data.publicKeyString ? data.publicKeyString : "");
                },
                failure: function(err) {
                    console.err(err);
                }
            });
        });

        $('#btn-load').on('click', function(){
            var adminId = $("#admin-id").val();
            if (!adminId) {
                alert('Please provide the admin ID');
                return;
            }
            var requestId = $("#request-id").val();
            $.ajax({
                type: "GET",
                url: "/api/datarequest/" + requestId,
                data: null,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    $("#client-email").val(data.email ? data.email : "");
                    $("#encrypt-password").val("");
                    $("#decrypt-password").val("");
                    $("#data-1").val(data.data1 ? data.data1 : "");
                    $("#data-2").val(data.data2 ? data.data2 : "");
                    $("#data-3").val(data.data3 ? data.data3 : "");
                    $("#data-4").val(data.data4 ? data.data4 : "");
                    $("#shared-secret").val("");
                    if (data.encryptedSharedSecretList && data.encryptedSharedSecretList[adminId]) {
                        $("#shared-secret").val(data.encryptedSharedSecretList[adminId].encryptedSharedSecret);
                    }
                },
                failure: function(err) {
                    console.err(err);
                }
            });
        });

        $('#btn-decrypt').on('click', function(){
            var data1 = $("#data-1").val();
            var data2 = $("#data-2").val();
            var data3 = $("#data-3").val();
            var data4 = $("#data-4").val();
            var adminMessage = $("#admin-message").val();
            var sharedSecretEncryptedForAdmin = $("#shared-secret").val();
            var decryptPassword = $("#decrypt-password").val();
            var adminRsaKey = cryptico.generateRSAKey(decryptPassword, bits);
            var decryptedSharedSecret = cryptico.decrypt(sharedSecretEncryptedForAdmin, adminRsaKey).plaintext;
            var adminCreatedSharedKey = cryptico.generateRSAKey(decryptedSharedSecret, bits);
            var decryptedData1 = cryptico.decrypt(data1, adminCreatedSharedKey).plaintext;
            var decryptedData2 = cryptico.decrypt(data2, adminCreatedSharedKey).plaintext;
            var decryptedData3 = cryptico.decrypt(data3, adminCreatedSharedKey).plaintext;
            var decryptedData4 = cryptico.decrypt(data4, adminCreatedSharedKey).plaintext;
            var decryptedAdminMessage = cryptico.decrypt(adminMessage, adminCreatedSharedKey).plaintext;
            $("#data-1").val(decryptedData1);
            $("#data-2").val(decryptedData2);
            $("#data-3").val(decryptedData3);
            $("#data-4").val(decryptedData4);
            $("#admin-mesage").val(decryptedAdminMessage);
        });

        $('#btn-update').on('click', function(){
            var requestId = $("#request-id").val();
            var adminId = $("#admin-id").val();
            var decryptPassword = $("#decrypt-password").val();
            if (requestId === "") {
                alert("Please load the request first");
                return;
            }
            if (adminId === "") {
                alert("Please load the admin first");
                return;
            }
            if (decryptPassword === "") {
                alert("Please provide the password of the admin");
                return;
            }
            var adminMessage = $("#admin-message").val();
            var sharedSecretEncryptedForAdmin = $("#shared-secret").val();
            var adminRsaKey = cryptico.generateRSAKey(decryptPassword, bits);
            var decryptedSharedSecret = cryptico.decrypt(sharedSecretEncryptedForAdmin, adminRsaKey).plaintext;
            var sharedRsaKey = cryptico.generateRSAKey(decryptedSharedSecret, bits);
            var sharedSecretKeyPublicKeyString = cryptico.publicKeyString(sharedRsaKey);
            var encryptedAdminMessage = cryptico.encrypt(adminMessage, sharedSecretKeyPublicKeyString).cipher;
            var request = {
                adminMessage: encryptedAdminMessage
            }
            $.ajax({
                type: "PUT",
                url: "/api/datarequest/" + requestId,
                data: JSON.stringify(request),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    $("#client-email").val(data.email ? data.email : "");
                    $("#encrypt-password").val("");
                    $("#decrypt-password").val("");
                    $("#data-1").val(data.data1 ? data.data1 : "");
                    $("#data-2").val(data.data2 ? data.data2 : "");
                    $("#data-3").val(data.data3 ? data.data3 : "");
                    $("#data-4").val(data.data4 ? data.data4 : "");
                    $("#shared-secret").val("");
                    if (data.encryptedSharedSecretList && data.encryptedSharedSecretList[adminId]) {
                        $("#shared-secret").val(data.encryptedSharedSecretList[adminId].encryptedSharedSecret);
                    }
                },
                failure: function(err) {
                    console.err(err);
                }
            });
        });
    </script>
</body>
</html>
