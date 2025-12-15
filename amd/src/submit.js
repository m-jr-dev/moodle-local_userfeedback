define(['jquery', 'core/ajax', 'core/str', 'core/templates'], function($, Ajax, Str, Templates) {
    return {
        init: function() {

            var root = $('#local-userfeedback-root');
            if (!root.length) {
                $('body').append('<div id="local-userfeedback-root"></div>');
                root = $('#local-userfeedback-root');
            }

            Templates.render('local_userfeedback/feedback', {})
                .then(function(html, js) {

                    root.html(html);

                    $('body').append('<div id="uf-launcher">Feedback</div>');

                    Str.get_string('rateyourxp', 'local_userfeedback').then(function(s) {
                        $('.uf-title').text(s);
                    });

                    function posicionarLauncher() {
                        var widget = $('#uf-widget');
                        var card = $('#uf-widget .ufb-card');
                        var launcher = $('#uf-launcher');
                        var wasHidden = widget.hasClass('uf-hidden');

                        if (wasHidden) {
                            widget.removeClass('uf-hidden').css('visibility', 'hidden');
                        }

                        var hCard = card.outerHeight();
                        var hBtn = launcher.outerHeight();
                        var posTop = (hCard / 2) - (hBtn / 2);

                        launcher.css({
                            top: 'calc(53% + ' + posTop + 'px)'
                        });

                        if (wasHidden) {
                            widget.addClass('uf-hidden').css('visibility', '');
                        }
                    }

                    posicionarLauncher();
                    $(window).on('resize', posicionarLauncher);

                    $(document).on('click', '#uf-launcher', function() {

                        var widget = $('#uf-widget');
                        var launcher = $('#uf-launcher');

                        widget.toggleClass('uf-hidden');

                        if (!widget.hasClass('uf-hidden')) {
                            launcher.css({
                                right: (widget.outerWidth() + 20) + 'px'
                            });
                            posicionarLauncher();
                        } else {
                            launcher.css({
                                right: '0px'
                            });
                            posicionarLauncher();
                        }
                    });

                    $(document)
                        .off('click.local_userfeedback_rate', '#uf-ratings .uf-rate')
                        .on('click.local_userfeedback_rate', '#uf-ratings .uf-rate', function() {
                            $('#uf-ratings .uf-rate').removeClass('selected').css('opacity', 0.6);
                            $(this).addClass('selected').css('opacity', 1);
                        });

                    $('#uf-ratings .uf-rate').css('opacity', 0.85);

                    $(document)
                        .off('click.local_userfeedback_submit', '#uf-submit')
                        .on('click.local_userfeedback_submit', '#uf-submit', function() {

                            var rating = $('#uf-ratings .selected').data('rate');
                            var comment = $('#uf-comment').val();
							
							if (!rating) {
							$('#uf-message')
								.removeClass()
								.addClass('alert alert-warning') 
								.text('Por favor, selecione uma avaliação.')
								.fadeIn(200);
							return;
						}
                            Ajax.call([{
                                methodname: 'local_userfeedback_submit_feedback',
                                args: { rating: rating, comment: comment }
                            }])[0]
                            .done(function(response) {

                                $('#uf-message')
								.removeClass()
								.addClass('alert alert-success')
								.text(response.message)
								.fadeIn(200);

                                setTimeout(function() {
                                    $('#uf-widget').fadeOut(400);
                                    $('#uf-launcher').fadeOut(400);
                                    posicionarLauncher();
                                }, 4200);

                            })
                            .fail(function() {
                                .removeClass()
                                .addClass('alert alert-danger')
								.text('Erro ao enviar')
                                .fadeIn(200);
                            });
                        });

                    Templates.runTemplateJS(js);
                });
        }
    };
});
