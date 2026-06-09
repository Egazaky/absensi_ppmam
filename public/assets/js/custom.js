/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 * 
 */

"use strict";

$(function () {
  $('[title]').tooltip({
    container: 'body',
    trigger: 'hover'
  });

  $('.alert:not(.alert-danger)').delay(4500).fadeOut(250);

  $('form').on('submit', function () {
    var form = $(this);

    // If form has client-side validation and is invalid, don't change button loading state
    if (form.hasClass('needs-validation') && form[0] && form[0].checkValidity() === false) {
      return;
    }

    var submitButton = form.find('button[type="submit"]').filter(':visible').first();

    if (!submitButton.length || submitButton.data('skip-loading')) {
      return;
    }

    submitButton.data('original-html', submitButton.html());
    submitButton.prop('disabled', true);
    submitButton.html('<i class="fas fa-circle-notch fa-spin mr-1"></i> Memproses');
  });

  $('.table tbody tr').each(function () {
    var row = $(this);
    var firstLink = row.find('td a[href]').not('[href^="javascript"]').first();

    if (!firstLink.length || row.find('td[colspan]').length) {
      return;
    }

    row.addClass('js-row-focus');
    row.on('dblclick', function (event) {
      if ($(event.target).closest('a, button, input, select, textarea, label').length) {
        return;
      }

      window.location.href = firstLink.attr('href');
    });
  });

  $('.js-row-focus').attr('title', 'Double click untuk membuka detail');

  // Persist sidebar state
  $(document).on('click', "[data-toggle='sidebar']", function () {
    setTimeout(function () {
      var isMini = $('body').hasClass('sidebar-mini');
      try {
        localStorage.setItem('sidebar_mini', isMini ? 'true' : 'false');
      } catch (e) {}
    }, 50);
  });
});
