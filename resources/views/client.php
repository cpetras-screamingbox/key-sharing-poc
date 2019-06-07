<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
        }
        th {
            font-weight: bold;
        }
    </style>
    <script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
    <!-- https://github.com/wwwtyro/cryptico -->
    <script src="/cryptico.js"></script>
    <script>
        var bits = 1024;
    </script>
    <script>
        function loadAdminList()
        {
            $.ajax({
                type: "GET",
                url: "/api/admin",
                data: null,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    $("#admin-list").html("");
                    for (adminId in data) {
                        addAdmin(adminId, data[adminId]);
                    }
                },
                failure: function(err) {
                    console.err(err);
                }
            });
        }
        function addAdmin(adminId, adminData)
        {
            var tbody = $('#admin-list');
            var tr = $("<tr class='admin-item'><td class='id'>" + adminId  + "</td><td class='email'>" + (adminData.email ? adminData.email : "") + "</td><td class='public-key-string'>" + (adminData.publicKeyString ? adminData.publicKeyString : "") + "</td></tr>");
            tr.appendTo(tbody);
        }
    </script>
</head>
<body>
    <h1>SUBMIT NEW REQUEST</h1>
    Email: <input id="email" type="text" name="email" />
    Encrypt with Password: <input id="encrypt-password" type="text" name="encrypt-password" />
    <hr />
    Data 1: <input id="data-1" type="text" name="data-1" /><br />
    Data 2: <input id="data-2" type="text" name="data-2" /><br />
    Data 3: <input id="data-3" type="text" name="data-3" /><br />
    Data 4: <input id="data-4" type="text" name="data-4" /><br />
    Admin message: <input id="admin-message" type="text" name="admin-message" disabled /><br />
    Encrypted shared secret for admins: <textarea id="encrypted-shared-secret-list" type="text" disabled></textarea>
    <br />
    <button id="btn-encrypt">Encrypt</button>
    <button id="btn-submit">Save</button>
    <hr />
    <h1>LOAD REQUEST</h1>
    Request ID: <input id="request-id" type="text" name="requet-id" /><br />
    Decrypt with password: <input id="decrypt-password" type="text" name="decrypt-password" /><br />
    <br />
    <button id="btn-load">Load</button>
    <button id="btn-decrypt">Decrypt</button>
    <hr />
    <h1>LOAD ADMINS</h1>
    <h2>Data will be shared with the loaded admins</h2>
    <table>
        <thead>
            <th>ID</th>
            <th>Email</th>
            <th>Public key string</th>
        </thead>
        <tbody id="admin-list"></tbody>
    </table>
    <script>
        loadAdminList();
    </script>
    <br />
    <button id="btn-load-admins">Load admins</button>
    <script>
        $('#btn-encrypt').on('click', function(){
            var email = $("#email").val();
            var encryptPassword = $("#encrypt-password").val();
            var data1 = $("#data-1").val();
            var data2 = $("#data-2").val();
            var data3 = $("#data-3").val();
            var data4 = $("#data-4").val();
            var clientRsaKey = cryptico.generateRSAKey(encryptPassword, bits);
            var clientPublicKeyString = cryptico.publicKeyString(clientRsaKey);
            var sharedSecret = clientPublicKeyString;
            var sharedRsaKey = cryptico.generateRSAKey(sharedSecret, bits);
            var sharedPublicKeyString = cryptico.publicKeyString(sharedRsaKey);
            var data1encrypted = cryptico.encrypt(data1, sharedPublicKeyString).cipher;
            var data2encrypted = cryptico.encrypt(data2, sharedPublicKeyString).cipher;
            var data3encrypted = cryptico.encrypt(data3, sharedPublicKeyString).cipher;
            var data4encrypted = cryptico.encrypt(data4, sharedPublicKeyString).cipher;
            $("#data-1").val(data1encrypted);
            $("#data-2").val(data2encrypted);
            $("#data-3").val(data3encrypted);
            $("#data-4").val(data4encrypted);
            var elList = $('.admin-item');
            var encryptedSharedSecretList = {};
            for (var i = 0; i < elList.length; i++) {
                var adminId = $(elList[i]).find('.id').html();
                var adminPublicKeyString = $(elList[i]).find('.public-key-string').html();
                var sharedSecretEncryptedForAdmin = cryptico.encrypt(sharedSecret, adminPublicKeyString).cipher;
                encryptedSharedSecretList[adminId] = {
                    encryptedSharedSecret: sharedSecretEncryptedForAdmin
                }
            }
            $('#encrypted-shared-secret-list').html(JSON.stringify(encryptedSharedSecretList));

        });

        $('#btn-submit').on('click', function(){
            var email = $("#email").val();
            var data1 = $("#data-1").val();
            var data2 = $("#data-2").val();
            var data3 = $("#data-3").val();
            var data4 = $("#data-4").val();
            var encryptedSharedSecretList = $("#encrypted-shared-secret-list").val();
            var request = {
                email: email,
                data1: data1,
                data2: data2,
                data3: data3,
                data4: data4,
                encryptedSharedSecretList: JSON.parse(encryptedSharedSecretList)
            }
            $.ajax({
                type: "POST",
                url: "/api/datarequest",
                data: JSON.stringify(request),
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    $("#request-id").val(data.id);
                    $("#email").val("");
                    $("#encrypt-password").val("");
                    $("#data-1").val("");
                    $("#data-2").val("");
                    $("#data-3").val("");
                    $("#data-4").val("");
                },
                failure: function(err) {
                    console.err(err);
                }
            });
        });

        $('#btn-load').on('click', function(){
            var requestId = $("#request-id").val();
            $.ajax({
                type: "GET",
                url: "/api/datarequest/" + requestId,
                data: null,
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(data){
                    $("#email").val(data.email ? data.email : "");
                    $("#encrypt-password").val("");
                    $("#decrypt-password").val("");
                    $("#data-1").val(data.data1 ? data.data1 : "");
                    $("#data-2").val(data.data2 ? data.data2 : "");
                    $("#data-3").val(data.data3 ? data.data3 : "");
                    $("#data-4").val(data.data4 ? data.data4 : "");
                    $("#admin-message").val(data.adminMessage ? data.adminMessage : "");
                    $("#encrypted-shared-secret-list").val(data.encryptedSharedSecretList ? JSON.stringify(data.encryptedSharedSecretList) : "");
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
            var decryptPassword = $("#decrypt-password").val();
            var clientRsaKey = cryptico.generateRSAKey(decryptPassword, bits);
            var clientPublicKeyString = cryptico.publicKeyString(clientRsaKey);
            var sharedSecret = clientPublicKeyString;
            var sharedRsaKey = cryptico.generateRSAKey(sharedSecret, bits);
            var decryptedData1 = cryptico.decrypt(data1, sharedRsaKey).plaintext;
            var decryptedData2 = cryptico.decrypt(data2, sharedRsaKey).plaintext;
            var decryptedData3 = cryptico.decrypt(data3, sharedRsaKey).plaintext;
            var decryptedData4 = cryptico.decrypt(data4, sharedRsaKey).plaintext;
            var decryptedAdminMessage = (adminMessage == "") ? "" : cryptico.decrypt(adminMessage, sharedRsaKey).plaintext;
            $("#data-1").val(decryptedData1);
            $("#data-2").val(decryptedData2);
            $("#data-3").val(decryptedData3);
            $("#data-4").val(decryptedData4);
            $("#admin-message").val(decryptedAdminMessage);
        });

        $('#btn-load-admins').on('click', function(){
            loadAdminList();
        });
    </script>
</body>
</html>
