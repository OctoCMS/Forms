if ($) {
    $(document).ready(function () {
        $('form').each(function () {
            var thisForm = $(this);

            if (thisForm.data('validation') == 'on') {

                thisForm.find('input[title]').tooltip({
                    placement: 'bottom',
                    trigger: 'focus'
                });

                // Credit cards fields:
                var ccNumber = thisForm.find('input.cc-number');

                if (ccNumber.length && typeof ccNumber.payment != "undefined") {
                    var ccExpiry = thisForm.find('input.cc-expiry');
                    var ccCvc = thisForm.find('input.cc-cvc');

                    // Set up formatting:
                    ccNumber.payment('formatCardNumber');
                    ccExpiry.payment('formatCardExpiry');
                    ccCvc.payment('formatCardCVC');

                    ccExpiry.on('change paste', function () {
                        validateCardDetails(ccNumber, ccExpiry, ccCvc);
                    });

                    ccCvc.on('change paste', function () {
                        validateCardDetails(ccNumber, ccExpiry, ccCvc);
                    });
                }

                if (typeof formatLocal != "undefined") {
                    thisForm.find('input.phone').on('keyup', function (e) {
                        if (e.keyCode == 9 || e.keyCode == 91) {
                            return;
                        }

                        $(this).val(formatLocal('GB', $(this).val()));
                    });

                    thisForm.find('input.phone').on('change', function (e) {
                        $(this).val(formatLocal('GB', $(this).val()));

                        var isValid = isValidNumber($(this).val(), 'GB');

                        if (isValid) {
                            if ($(this).find('~ .fa-check').length == 0) {
                                $(this).after($('<i class="fa fa-check"></i>'));
                            }

                            $(this).removeClass('invalid');
                            $(this).addClass('valid');
                        } else if (($(this).prop('required') || $(this).val().trim() != '') && !isValid) {
                            if ($(this).find('~ .fa-check').length == 0) {
                                $(this).after($('<i class="fa fa-check"></i>'));
                            }

                            $(this).removeClass('valid');
                            $(this).addClass('invalid');
                        } else {
                            $(this).removeClass('valid');
                            $(this).removeClass('invalid');
                            $(this).find('~ .fa-check').remove();
                        }
                    });
                }

                thisForm.find('input.postcode').on('change', function () {
                    var pc = checkPostCode($(this).val());

                    if (pc) {
                        if ($(this).find('~ .fa-check').length == 0) {
                            $(this).after($('<i class="fa fa-check"></i>'));
                        }

                        $(this).val(pc);
                        $(this).removeClass('invalid');
                        $(this).addClass('valid');
                    } else if (($(this).prop('required') || $(this).val().trim() != '') && !pc) {
                        if ($(this).find('~ .fa-check').length == 0) {
                            $(this).after($('<i class="fa fa-check"></i>'));
                        }

                        $(this).removeClass('valid');
                        $(this).addClass('invalid');
                    } else {
                        $(this).removeClass('valid');
                        $(this).removeClass('invalid');
                        $(this).find('~ .fa-check').remove();
                    }
                });

                thisForm.find('input[type=email]').on('change', function () {
                    var pc = isValidEmail($(this).val());

                    if (pc) {
                        if ($(this).find('~ .fa-check').length == 0) {
                            $(this).after($('<i class="fa fa-check"></i>'));
                        }

                        $(this).removeClass('invalid');
                        $(this).addClass('valid');
                    } else if (($(this).prop('required') || $(this).val().trim() != '') && !pc) {
                        if ($(this).find('~ .fa-check').length == 0) {
                            $(this).after($('<i class="fa fa-check"></i>'));
                        }

                        $(this).removeClass('valid');
                        $(this).addClass('invalid');
                    } else {
                        $(this).removeClass('valid');
                        $(this).removeClass('invalid');
                        $(this).find('~ .fa-check').remove();
                    }
                });

                // Enable validation:
                thisForm.on('submit', function (e) {
                    var valid = true;
                    var warnings = [];

                    thisForm.find('input[required]').filter(':not(.cc), :not(.phone), :not(.postcode), :not(input[type=email])').each(function () {
                        if ($(this).attr('type') == 'checkbox') {
                            if (!$(this).prop('checked')) {
                                warnings.push({el: $(this), msg: 'Please fill in this field.', noCheck: true});
                                valid = false;
                            }
                        } else if ($(this).val().trim() == '') {
                            warnings.push({el: $(this), msg: 'Please fill in this field.'});
                            valid = false;
                        }
                    });

                    if (typeof isValidNumber != "undefined") {
                        thisForm.find('input.phone').each(function () {
                            if (($(this).prop('required') || $(this).val().trim() != '') && !isValidNumber($(this).val(), 'GB')) {
                                warnings.push({el: $(this), msg: 'Please enter a valid phone number.'});
                                valid = false;
                            }
                        });
                    }

                    thisForm.find('input.postcode').each(function () {
                        if (($(this).prop('required') || $(this).val().trim() != '') && !checkPostCode($(this).val())) {
                            warnings.push({el: $(this), msg: 'Please enter a valid postcode.'});
                            valid = false;
                        }
                    });

                    thisForm.find('input[type=email]').each(function () {
                        if (($(this).prop('required') || $(this).val().trim() != '') && !isValidEmail($(this).val())) {
                            warnings.push({el: $(this), msg: 'Please enter a valid email address.'});
                            valid = false;
                        }
                    });

                    if (!validateCardDetails(ccNumber, ccExpiry, ccCvc, true)) {
                        valid = false;
                    }

                    if (!valid) {
                        e.preventDefault();

                        for (var i in warnings) {
                            var el = warnings[i]['el'];
                            var msg = warnings[i]['msg'];

                            el.addClass('invalid');

                            if (el.attr('type') != 'checkbox') {
                                if (el.find('~ .fa-check').length == 0) {
                                    el.after($('<i class="fa fa-check"></i>'));
                                }
                            }

                            el.tooltip({
                                placement: 'bottom',
                                trigger: 'manual',
                                title: msg
                            }).tooltip('show');


                            el.on('change paste keyup', function () {
                                el.tooltip('hide');
                                el.removeClass('invalid');
                            });
                        }
                    }

                    if (valid) {
                        $(thisForm).trigger('validated-submit');
                    }

                    return valid;
                });
            }
        });
    });
}

function isValidEmail(email) {
    var reg = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
    return reg.test(email);
}

function validateCardDetails(ccNumber, ccExpiry, ccCvc, highlightCcNumber) {
    // If we don't have any credit card fields, then this validation check should pass:
    if (!ccNumber.length) {
        return true;
    }

    var expiry = ccExpiry.payment('cardExpiryVal');
    var validateNumber = $.payment.validateCardNumber(ccNumber.val());
    var validateExpiry = $.payment.validateCardExpiry(expiry["month"], expiry["year"]);
    var validateCVC = $.payment.validateCardCVC(ccCvc.val());
    var valid = true;

    if (validateExpiry) {
        ccExpiry.removeClass('invalid');
        ccExpiry.addClass('valid');
    } else {
        if (ccExpiry.val() != '') {
            ccExpiry.addClass('invalid');
        }

        ccExpiry.removeClass('valid');
        valid = false;
    }

    if (validateCVC) {
        ccCvc.removeClass('invalid');
        ccCvc.addClass('valid');
    } else {
        if (ccCvc.val() != '') {
            ccCvc.addClass('invalid');
        }

        ccCvc.removeClass('valid');
        valid = false;
    }

    if (validateNumber && highlightCcNumber) {
        ccNumber.removeClass('invalid');
        ccNumber.addClass('valid');

        $('.cc-type').val($.payment.cardType(ccNumber.val()));

    } else if (!validateNumber && highlightCcNumber) {
        ccNumber.addClass('invalid');
        ccNumber.removeClass('valid');

        valid = false;
    } else if (!validateNumber) {
        valid = false;
    }

    return valid;
}



/*==================================================================================================

 Application:   Utility Function
 Author:        John Gardner

 Version:       V1.0
 Date:          18th November 2003
 Description:   Used to check the validity of a UK postcode

 Version:       V2.0
 Date:          8th March 2005
 Description:   BFPO postcodes implemented.
 The rules concerning which alphabetic characters are allowed in which part of the
 postcode were more stringently implementd.

 Version:       V3.0
 Date:          8th August 2005
 Description:   Support for Overseas Territories added

 Version:       V3.1
 Date:          23rd March 2008
 Description:   Problem corrected whereby valid postcode not returned, and 'BD23 DX' was invalidly
 treated as 'BD2 3DX' (thanks Peter Graves)

 Version:       V4.0
 Date:          7th October 2009
 Description:   Character 3 extended to allow 'pmnrvxy' (thanks to Jaco de Groot)

 Version:       V4.1
 8th September 2011
 Support for Anguilla overseas territory added

 Version:       V5.0
 Date:          8th November 2012
 Specific support added for new BFPO postcodes

 Parameters:    toCheck - postcodeto be checked.

 This function checks the value of the parameter for a valid postcode format. The space between the
 inward part and the outward part is optional, although is inserted if not there as it is part of the
 official postcode.

 If the postcode is found to be in a valid format, the function returns the postcode properly
 formatted (in capitals with the outward code and the inward code separated by a space. If the
 postcode is deemed to be incorrect a value of false is returned.

 Example call:

 if (checkPostCode (myPostCode)) {
 alert ("Postcode has a valid format")
 }
 else {alert ("Postcode has invalid format")};

 --------------------------------------------------------------------------------------------------*/

function checkPostCode(toCheck) {

    // Permitted letters depend upon their position in the postcode.
    var alpha1 = "[abcdefghijklmnoprstuwyz]";                       // Character 1
    var alpha2 = "[abcdefghklmnopqrstuvwxy]";                       // Character 2
    var alpha3 = "[abcdefghjkpmnrstuvwxy]";                         // Character 3
    var alpha4 = "[abehmnprvwxy]";                                  // Character 4
    var alpha5 = "[abdefghjlnpqrstuwxyz]";                          // Character 5
    var BFPOa5 = "[abdefghjlnpqrst]";                               // BFPO alpha5
    var BFPOa6 = "[abdefghjlnpqrstuwzyz]";                          // BFPO alpha6

    // Array holds the regular expressions for the valid postcodes
    var pcexp = new Array ();

    // BFPO postcodes
    pcexp.push (new RegExp ("^(bf1)(\\s*)([0-6]{1}" + BFPOa5 + "{1}" + BFPOa6 + "{1})$","i"));

    // Expression for postcodes: AN NAA, ANN NAA, AAN NAA, and AANN NAA
    pcexp.push (new RegExp ("^(" + alpha1 + "{1}" + alpha2 + "?[0-9]{1,2})(\\s*)([0-9]{1}" + alpha5 + "{2})$","i"));

    // Expression for postcodes: ANA NAA
    pcexp.push (new RegExp ("^(" + alpha1 + "{1}[0-9]{1}" + alpha3 + "{1})(\\s*)([0-9]{1}" + alpha5 + "{2})$","i"));

    // Expression for postcodes: AANA  NAA
    pcexp.push (new RegExp ("^(" + alpha1 + "{1}" + alpha2 + "{1}" + "?[0-9]{1}" + alpha4 +"{1})(\\s*)([0-9]{1}" + alpha5 + "{2})$","i"));

    // Exception for the special postcode GIR 0AA
    pcexp.push (/^(GIR)(\s*)(0AA)$/i);

    // Standard BFPO numbers
    pcexp.push (/^(bfpo)(\s*)([0-9]{1,4})$/i);

    // c/o BFPO numbers
    pcexp.push (/^(bfpo)(\s*)(c\/o\s*[0-9]{1,3})$/i);

    // Overseas Territories
    pcexp.push (/^([A-Z]{4})(\s*)(1ZZ)$/i);

    // Anguilla
    pcexp.push (/^(ai-2640)$/i);

    // Load up the string to check
    var postCode = toCheck;

    // Assume we're not going to find a valid postcode
    var valid = false;

    // Check the string against the types of post codes
    for ( var i=0; i<pcexp.length; i++) {

        if (pcexp[i].test(postCode)) {

            // The post code is valid - split the post code into component parts
            pcexp[i].exec(postCode);

            // Copy it back into the original string, converting it to uppercase and inserting a space
            // between the inward and outward codes
            postCode = RegExp.$1.toUpperCase() + " " + RegExp.$3.toUpperCase();

            // If it is a BFPO c/o type postcode, tidy up the "c/o" part
            postCode = postCode.replace (/C\/O\s*/,"c/o ");

            // If it is the Anguilla overseas territory postcode, we need to treat it specially
            if (toCheck.toUpperCase() == 'AI-2640') {postCode = 'AI-2640'};

            // Load new postcode back into the form element
            valid = true;

            // Remember that we have found that the code is valid and break from loop
            break;
        }
    }

    // Return with either the reformatted valid postcode or the original invalid postcode
    if (valid) {return postCode;} else return false;
}
