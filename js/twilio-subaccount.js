;
window.TWILIO = window.TWILIO || {};

TWILIO.subaccount = (function ($, window, document, undefined) {
    "use strict";

    var response = [];

    var addHeader = true;

    function fetch(sid, token, pageNum) {
        TWILIO.util.progress("Fetching sub accounts, page: " + pageNum);
        $.ajax({
            type: "GET",
            url: "rest/sub-accounts/all/" + sid + "/" + token + "/" + pageNum,
            dataType: "json",
            xhrFields: {
                withCredentials: false
            },
            success: function (jsonResult) {
                if (jsonResult.error) {
                    TWILIO.util.error("Failed. " + jsonResult.error);
                }
                else {
                    if (addHeader) {
                        response.push(jsonResult.header);
                        addHeader = false;
                    }
                    response = response.concat(jsonResult.data);
                    if (jsonResult.hasNextPage) {
                        fetch(sid, token, pageNum + 1);
                    }
                    else {
                        TWILIO.util.download("sub_accounts.csv", response);
                    }
                }
            }
        });
    }

    $("#fetch-sub-accounts").on("click", function (event) {
        event.preventDefault();
        TWILIO.util.start("Start fetching sub accounts...");
        var sid = $("#sid").val(),
            token = $("#token").val();
        if (!sid || !token) {
            TWILIO.util.error("No master credentials set, exiting...").done();
        }
        else {
            response = [];
            addHeader = true;
            fetch(sid, token, 0);
        }
    });

    $("#status-sub-accounts-btn").on("click", function (event) {
        event.preventDefault();
        TWILIO.util.start("Changing sub-accounts status...");
        var file = $('#status-sub-accounts-file')[0].files[0];
        if (file) {
            var reader = new FileReader();
            reader.readAsText(file);
            reader.onload = function (e) {
                var parsed = $.csv.toArrays(e.target.result);
                if (parsed.length > 0) {
                    var sid = $("#status-sid").val().trim(),
                        token = $("#status-token").val().trim(),
                        status = $("#status-value-suspended").is(':checked') ? 'suspended' : 'active';
                    if (!sid || !token) {
                        TWILIO.util.error("No master credentials set, exiting...").done();
                    }
                    else {
                        BootstrapDialog.show({
                            title: 'Change Sub Accounts Status',
                            closable: false,
                            message: 'You are about to change status of ' + parsed.length + ' sub-accounts into <b>' + status + '</b>! Would you like to continue?',
                            buttons: [{
                                label: 'Yes',
                                action: function (dialog) {
                                    dialog.close();
                                    statusChange(sid, token, status, parsed);
                                }
                            }, {
                                label: 'No',
                                action: function (dialog) {
                                    TWILIO.util.progress("Action canceled by user.").done();
                                    dialog.close();
                                }
                            }]
                        });
                    }
                }
                else {
                    TWILIO.util.error("No data found in file, exiting...").done();
                }
            };
        }
        else {
            TWILIO.util.error("No input CSV file selected, exiting...").done();
        }
    });

    function statusChange(sid, token, status, sidArray) {
        var hasValidInput = false;
        $.each(sidArray, function (index, item) {
            if (TWILIO.util.validateSid(item)) {
                hasValidInput = true;
                $.ajax({
                    type: "POST",
                    url: "rest/sub-accounts/status/" + sid + "/" + token + "/" + item[0] + "/" + status,
                    dataType: "json",
                    xhrFields: {
                        withCredentials: false
                    },
                    success: function (jsonResult) {
                        if (jsonResult.error) {
                            TWILIO.util.error(jsonResult.error);
                        }
                        else {
                            TWILIO.util.success(jsonResult.success);
                        }
                    }
                });
            }
        });
        if (!hasValidInput) {
            TWILIO.util.done();
        }
    }

}(jQuery, window, document));
