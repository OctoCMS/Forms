if ($) {
    $(document).ready(function () {
        $('form').each(function () {
            var thisForm = $(this);

            if (thisForm.data('validation') == 'on') {
                thisForm.find('input[title]').tooltip({
                    placement: 'bottom',
                    trigger: 'focus'
                });


                // Enable validation:
                thisForm.on('submit', function (e) {
                    var valid = true;
                    var warnings = [];

                    if (thisForm.hasClass('octo-forms-form')) {
                        e.preventDefault();
                    }

                    thisForm.find('input[required]').filter(':not(.custom-validator)').each(function () {
                        validationReset($(this));

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

                    thisForm.find('input.custom-validator').each(function () {
                        var val = $(this).data('validator');

                        if (typeof val == 'function' && !val()) {
                            valid = false;
                        }
                    });


                    if (!valid) {
                        e.preventDefault();

                        for (var i in warnings) {
                            var el = warnings[i]['el'];
                            var msg = warnings[i]['msg'];

                            validationFailed(el);

                            el.tooltip({
                                placement: 'bottom',
                                trigger: 'manual',
                                title: msg
                            }).tooltip('show');


                            el.on('change paste keyup', function () {
                                validationReset(el);
                                el.tooltip('hide');
                            });
                        }
                    }

                    if (valid) {
                        $(thisForm).trigger('validated.octo');
                    }

                    return valid;
                });
            }
        });



        $('form.octo-forms-form').on('validated.octo', function () {
            var $form = $(this);
            var $error = $form.find('.octo-form-error');
            var $success = $form.find('.octo-form-success');
            var $btn = $form.find('button[type="submit"]');

            $success.hide();
            $error.hide();
            
            $btn.text('Submitting...');
            
            $.post('/form/submit', $(this).serialize()).always(function (data) {
                if (typeof data != "object") {
                    data = {
                        success: false,
                        message: "There was a problem submitting the form. Please try again."
                    };
                }

                if (data.success) {
                    $success.html(data.message);
                    $success.slideDown();
                    $btn.text('Submitted!');
                    return;
                }

                $error.html(data.message);
                $error.slideDown();
                $btn.text('Submit');

                if (data.fields) {
                    for (var i in data.fields) {
                        $form.find('.input-' + data.fields[i]).addClass('error');
                    }
                }
            });
        });
    });
}

function validationReset(field) {
    field.removeClass('valid');
    field.removeClass('invalid');

    field.parents('.form-group').removeClass('has-success');
    field.parents('.form-group').removeClass('has-danger');

    field.trigger('reset.octo');
}

function validationFailed(field) {
    field.addClass('invalid');
    field.parents('.form-group').addClass('has-danger');
    field.trigger('invalid.octo');
}

function validationSuccess(field) {
    field.addClass('valid');
    field.parents('.form-group').addClass('has-success');
    field.trigger('valid.octo');
}


/***********************************************
 * Phone Number Validation
 ***********************************************/
$(document).ready(function () {
    // If Google's phoneformat library is not defined, skip all:
    if (typeof formatLocal == 'undefined') {
        return;
    }

    $('.octo-form .phone').each(function () {
        var phone = $(this);
        var parent = phone.parents('form').first();
        var country = parent.find('.country');
        var hasCountry = country.length ? true : false;

        if (parent.data('validation') != 'on') {
            return;
        }

        phone.addClass('custom-validator');

        var getCurrentCountry = function () {
            return hasCountry ? country.val() : 'GB';
        };

        // Set up the custom validator:
        phone.data('validator', function () {
            validationReset(phone);

            var result = isValidNumber(phone.val(), getCurrentCountry());

            if (result) {
                phone.val(formatLocal(getCurrentCountry(), phone.val()));
                validationSuccess(phone);
                return true;
            }

            if (!result && (phone.prop('required') || phone.val().trim() != '')) {
                validationFailed(phone);
                return false;
            }

            return true;
        });

        if (hasCountry) {
            country.on('change', function () {
                phone.trigger('change');
            });
        }

        phone.on('change', phone.data('validator'));

        if (phone.val() != '') {
            phone.val(formatLocal(getCurrentCountry(), phone.val()));
            phone.trigger('change');
        }
    });

});


/***********************************************
 * Email Address Validation
 ***********************************************/
$(document).ready(function () {
    $('.octo-form input[type=email]').each(function () {
        var email = $(this);
        var parent = email.parents('form').first();


        if (parent.data('validation') != 'on') {
            return;
        }

        // Set up the custom validator:
        email.data('validator', function () {
            validationReset(email);

            var result = isValidEmail(email.val());

            if (result) {
                validationSuccess(email);
                return true;
            }

            if (!result && (email.prop('required') || email.val().trim() != '')) {
                validationFailed(email);
                return false;
            }

            return true;
        });

        email.on('change', email.data('validator'));
        email.addClass('custom-validator');

        if (email.val() != '') {
            email.trigger('change');
        }
    });
});


function isValidEmail(email) {
    var reg = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
    return reg.test(email);
}


/***********************************************
 * Password Validation
 ***********************************************/
$(document).ready(function () {
    $('.octo-form input[type=password]').each(function () {
        var password = $(this);
        var parent = password.parents('form').first();


        if (parent.data('validation') != 'on') {
            return;
        }

        // Set up the custom validator:
        password.data('validator', function (e) {
            validationReset(password);

            var result = (password.val().length >= 7);

            if (result) {
                validationSuccess(password);
                return true;
            }

            if (!result && (password.prop('required') || password.val().trim() != '')) {
                validationFailed(password);
                return false;
            }

            return true;
        });

        password.on('change', password.data('validator'));
        password.on('keyup', password.data('validator'));
        password.addClass('custom-validator');

        if (password.val() != '') {
            password.trigger('change');
        }
    });
});


/***********************************************
 * Credit Card Validation
 ***********************************************/

$(document).ready(function () {
    $('.octo-form').each(function () {
        var thisForm = $(this);
        var ccNumber = thisForm.find('input.cc-number');

        if (thisForm.data('validation') != 'on') {
            return;
        }

        if (ccNumber.length && typeof ccNumber.payment != "undefined") {
            var ccExpiry = thisForm.find('input.cc-expiry');
            var ccCvc = thisForm.find('input.cc-cvc');

            // Set up formatting:
            ccNumber.payment('formatCardNumber');
            ccExpiry.payment('formatCardExpiry');
            ccCvc.payment('formatCardCVC');

            ccNumber.addClass('custom-validator');
            ccExpiry.addClass('custom-validator');
            ccCvc.addClass('custom-validator');

            // Set up custom validators:
            ccNumber.data('validator', function (e) {
                validationReset(ccNumber);

                var result = $.payment.validateCardNumber(ccNumber.val());

                if (result) {
                    validationSuccess(ccNumber);
                    $('.cc-type').val($.payment.cardType(ccNumber.val()));
                    return true;
                }

                if (!result && (ccNumber.prop('required') || ccNumber.val().trim() != '')) {
                    validationFailed(ccNumber);
                    return false;
                }

                return true;
            }).on('change paste', ccNumber.data('validator'));

            ccExpiry.data('validator', function (e) {
                validationReset(ccExpiry);

                var expiry = ccExpiry.payment('cardExpiryVal');
                var result = $.payment.validateCardExpiry(expiry["month"], expiry["year"]);

                $(this).data('month', expiry["month"]);
                $(this).data('year', expiry["year"]);

                if (result) {
                    validationSuccess(ccExpiry);
                    return true;
                }

                if (!result && (ccExpiry.prop('required') || ccExpiry.val().trim() != '')) {
                    validationFailed(ccExpiry);
                    return false;
                }

                return true;
            }).on('change paste', ccExpiry.data('validator'));

            ccCvc.data('validator', function (e) {
                validationReset(ccCvc);

                var result = $.payment.validateCardCVC(ccCvc.val());

                if (result) {
                    validationSuccess(ccCvc);
                    return true;
                }

                if (!result && (ccCvc.prop('required') || ccCvc.val().trim() != '')) {
                    validationFailed(ccCvc);
                    return false;
                }

                return true;
            }).on('change paste', ccCvc.data('validator'));
        }
    });
});


/***********************************************
 * Postcode Validation
 ***********************************************/

$(document).ready(function () {

    $('.octo-form .postcode').each(function () {
        var pc = $(this);
        var parent = pc.parents('fieldset, form').first();
        var country = parent.find('.country');
        var hasCountry = country.length ? true : false;

        if (pc.parents('form').data('validation') != 'on') {
            return;
        }

        // As long as the country:
        if (!hasCountry || country.val() == 'GB') {
            pc.addClass('custom-validator');
        }

        // Set up the custom validator:
        pc.data('validator', function () {
            validationReset(pc);

            var result = checkPostCode(pc.val());

            if (result) {
                pc.val(result);
                validationSuccess(pc);
                return true;
            }

            if (!result && (pc.prop('required') || pc.val().trim() != '')) {
                validationFailed(pc);
                return false;
            }

            return true;
        });

        country.on('change', function () {
            if (country.val() == 'GB') {
                pc.on('change', pc.data('validator'));
                pc.addClass('custom-validator');
                pc.trigger('change');
            } else {
                validationReset(pc);
                pc.off('change');
                pc.removeClass('custom-validator');
            }
        });

        pc.on('change', pc.data('validator'));

        if (pc.val() != '') {
            pc.trigger('change');
        }
    });

});

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
