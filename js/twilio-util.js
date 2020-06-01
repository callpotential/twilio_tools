;
window.TWILIO = window.TWILIO || {};

TWILIO.util = (function ($, window, document, undefined) {
    "use strict";

    var me = {
        start: function (text) {
            $(".tab-content").LoadingOverlay("show");
            $("#progress-div").empty();
            $("#progress-div").show().append(text + "<br/>");
            $(document).off("ajaxStop").one("ajaxStop", function () {
                me.done();
            });
            return me;
        },
        done: function () {
            if ($("#progress-div").find(".js-task-done").length === 0) {
                $("#progress-div").append("<span class='js-task-done'>Done.</span><br/>");
            }
            $(".tab-content").LoadingOverlay("hide", true);
            return me;
        },
        progress: function (text) {
            $("#progress-div").append(text + "<br/>");
            return me;
        },
        error: function (text) {
            text = me.descriptiveError(text);
            $("#progress-div").append("<span style='color: #f00;'>ERROR: " + text + "</span><br/>");
            return me;
        },
        success: function (text) {
            $("#progress-div").append("<span style='color: #5cb85c;'>" + text + "</span><br/>");
            return me;
        },
        warn: function (text) {
            $("#progress-div").append("<span style='color: #b4b42d;'>WARN: " + text + "</span><br/>");
            return me;
        },
        download: function (fileName, data) {
            var csvData = $.csv.fromArrays(data);
            var blob = new Blob([csvData], {
                type: "application/csv;charset=utf-8;"
            });

            if (window.navigator.msSaveBlob) {
                // FOR IE BROWSER
                navigator.msSaveBlob(blob, fileName);
            } else {
                // FOR OTHER BROWSERS
                var link = document.createElement("a");
                var csvUrl = URL.createObjectURL(blob);
                link.href = csvUrl;
                link.style = "visibility:hidden";
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
            return me;
        },
        validateSid: function (sidItem, skipWarn) {
            var result = false;
            if (Object.prototype.toString.call(sidItem) === '[object Array]') {
                if (sidItem.length >= 1 &&
                    sidItem[0].length === 34 &&
                    sidItem[0].indexOf("AC") === 0) {
                    result = true;
                }
            }
            !skipWarn && !result && TWILIO.util.warn("Invalid line in the input file. Could be column header, empty line or invalid SID. Line: >" + sidItem + "<");
            return result;
        },
        validateSidToken: function (sidTokenPair, skipWarn) {
            var result = me.validateSid(sidTokenPair, true);
            if (result && (
                sidTokenPair.length < 2 ||
                sidTokenPair[1].length !== 32)) {
                result = false;
            }
            !skipWarn && !result && TWILIO.util.warn("Invalid line in the input file. Could be column header, empty line or invalid SID/token pair. Line: >" + sidTokenPair + "<");
            return result;
        },
        validateSidTokenRecSid: function (sidTokenRecSid, skipWarn) {
            var result = me.validateSidToken(sidTokenRecSid, true);
            if (result && (
                sidTokenRecSid.length < 3 ||
                sidTokenRecSid[2].length !== 34 ||
                sidTokenRecSid[2].indexOf("RE") !== 0)) {
                result = false;
            }
            !skipWarn && !result && TWILIO.util.warn("Invalid line in the input file. Could be column header, empty line or invalid SID/token/RecordingSid. Line: >" + sidTokenRecSid + "<");
            return result;
        },
        validateSidTokenPhoneSid: function (sidTokenPhoneSid, skipWarn) {
            var result = me.validateSidToken(sidTokenPhoneSid, true);
            if (result && (
                sidTokenPhoneSid.length < 3 ||
                sidTokenPhoneSid[2].length !== 34 ||
                sidTokenPhoneSid[2].indexOf("PN") !== 0)) {
                result = false;
            }
            !skipWarn && !result && TWILIO.util.warn("Invalid line in the input file. Could be column header, empty line or invalid SID/token/PhoneNumberSid. Line: >" + sidTokenPhoneSid + "<");
            return result;
        },
        descriptiveError: function (error) {
            if (error.indexOf("(401)")) {
                return error + ". Account may be suspended or credentials are wrong!";
            }
            else if (error.indexOf("(404)")) {
                return error + ". Does requested resource exist? Already deleted?";
            }
            else {
                return error;
            }
        }
    };

    return me;

}(jQuery, window, document));