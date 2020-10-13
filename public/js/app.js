$(document).ready(function () {

// invio email di invito
	$('#request-token').submit( function() {
		$('#request-token-email').removeClass('is-valid');
		$('#request-token-email').removeClass('is-invalid');
		$('#request-token-feedback').text('');
		var email = $('#request-token-email').val();
		if (!email || !email.match(/.+@.+/)) {
			$('#request-token-email').addClass('is-invalid');
			$('#request-token-feedback').text('Non hai inserito un indirizzo email valido');
		} else {
			$.get('/api/manda-invito', { email: email }, function(result) {
				console.log(result);
				if (result.status == 'error') {
					var error_message = 'Si è verificato un errore';
					switch (result.error) {
						case 'missing-email': error_message = 'Email non indicata'; break;
						case 'email-not-found': error_message = 'Email non trovata: forse non sei un elettore registrato.'; break;
						case 'user-invalid': error_message = 'Il tuo diritto di voto è al momento sospeso.'; break;
					}
					$('#request-token-email').addClass('is-invalid');
					$('#request-token-feedback').text(error_message);
				} else {
					$('#request-token-email').addClass('is-valid');
				}
			}, 'json');
		}
		return false;
	});		

	$('.rule-detail').click( function() {
		$(this).parents('div').children('.detail').slideToggle();
		return false;
	});
	
	$('.btn-save').click( function() {
		var url = $( "#vote-form" ).attr('action');
		if (!$('#confirm').is(':checked')) {
			$('.alert-danger.save-alert').html('Prima di salvare devi <a href="#" onclick="scroll_to(\'#confirm\')">confermare la tua identità</a>');
			$('.alert-danger.save-alert').fadeIn();
			setTimeout(function() { $('.save-alert').fadeOut(); }, 5000);
		} else {
			var formdata = $( "#vote-form" ).serialize();
			$.post(url, $( "#vote-form" ).serialize(), function(result) {
				if (result.status == 'success') {
					$('.alert-success.save-alert').fadeIn();
					setTimeout(function() { $('.save-alert').fadeOut(); }, 5000);
				} else {
					$('.alert-danger.save-alert').html(result.error);
					$('.alert-danger.save-alert').fadeIn();
					setTimeout(function() { $('.save-alert').fadeOut(); }, 5000);
				}
			},'json');
		}
		return false;
	});
	
	$('.btn-send-confirm').click( function() {
		if (!$('#confirm').is(':checked')) {
			$('.alert-danger.save-alert').html('Prima di salvare devi <a href="#" onclick="scroll_to(\'confirm\')">confermare la tua identità</a>');
			$('.alert-danger.save-alert').fadeIn();
		} else {
			if (confirm("Una volta inviato il voto non potrà più essere modificato per nessun motivo. Confermi l'invio?")) {	
				$("#vote-form input[name=action]").val('send');
				$( "#vote-form" ).submit();
			}
		}
	});		
	
	$('.btn-send').click( function() {
		if (!$('#confirm').is(':checked')) {
			$('.alert-danger.save-alert').html('Prima di salvare devi <a href="#" onclick="scroll_to(\'confirm\')">confermare la tua identità</a>');
			$('.alert-danger.save-alert').fadeIn();
		} else {
			$('#confirmSend').modal();
		}
	});
	
	$('#confirm').click( function() {
		$('.save-alert').fadeOut();
		stickyIdentity();
	});
	
	stickyIdentity();
	
	$('[data-countdown]').each(function() {
		countdown($(this).data('countdown'));
	});
});

jQuery.fn.extend({

	choice: function(i, value = undefined) {
		if (value !== undefined) $(this).find('input[name="'+$(this).data('cat')+'\\['+i+'\\]"]').val( value );
		var val = $(this).find('input[name="'+$(this).data('cat')+'\\['+i+'\\]"]').val();
		if (i==0 && val==0) return 'U';
		else return val;
	},
	
	choiceZero: function(val) {
		$(this).choice(1, val);
		$(this).choice(2, '0');
		$(this).choice(3, '0');
	},
		
	choiceUp: function(val, target = 0) {
		if ($(this).choice(1) == 'N' || $(this).choice(1) == 'U') $(this).choice(1,0); // togli non voto

		if (target == 0) {
			target = $(this).valPosition(val) - 1;
			if (target == 0) {
				$(this).choice(1, $(this).choice(2) );
				$(this).choice(2, $(this).choice(3) );
				$(this).choice(3, 0 );
				return;
			}
		} 
		console.log($(this).choice(2));
		if (target <= 0) target = 3;
		if ((target == 3) && ($(this).choice(2) == 0)) target = 2;
		if ((target == 2) && ($(this).choice(1) == 0)) target = 1;
		
		console.log(target);
		var previous = $(this).choice(target);
		$(this).choice(target, val);
		if (target < 3 && previous > 0) $(this).choice(target + 1, previous);
	},
	
	valPosition: function(val) {
		if ($(this).choice(1) == val) return 1;
		if ($(this).choice(2) == val) return 2;
		if ($(this).choice(3) == val) return 3;
		return 0;
	}
	
});

function alboSearch(text) {
	if (!text) {
		$('.albo-anno').slideDown();
		$('[data-desc]').slideDown();
		$('#filterFound').html('');
	} else {
		var text = text.toLowerCase();
			$('[data-desc]:not([data-desc*='+text+'])').slideUp();
		$('[data-desc*='+text+']').slideDown();
		$('.albo-anno:not(:has([data-desc*='+text+']))').slideUp();
		$('.albo-anno:has([data-desc*='+text+'])').slideDown();
		var found = $('[data-desc*='+text+']').length;
		if (found == 0) found = '';
		else if (found == 1) found += ' premio trovato';
		else found += ' premi trovati';
		$('#filterFound').html(found);
	}
}

$(document).ready(function () {
	$('.finalisti button').click( function() {
		var $parent = $(this).parents('.finalisti');
		var val = $(this).data('value');
		var pos = $(this).data('position');

		console.log($parent);
		console.log(val);
		console.log(pos);

		if (val == 'U' || val == 'N') {
			$parent.choiceZero(val);
			$parent.find('button.activable').removeClass('active');
			$parent.find('button[data-value="'+val+'"].activable').addClass('active');
		} else {
			$parent.find('button.activable').removeClass('active');
			$parent.choiceUp(val);
			for (i=1; i<=3; i++) $parent.find('button[data-value="'+$parent.choice(i)+'"][data-position='+i+'].activable').addClass('active');
		}
		return false;
	});
	
	openAggiungi();
	
	$(window).bind('hashchange', function() {
		openAggiungi();
	});
	
	$('#candidatura-form').submit( function() {
		var fields = $(this).serializeArray();
		var complete = true;
		jQuery.each( fields, function( i, field ) { if (!field.value) complete = false; });
		if (!complete) {
			$('.alert-danger.candidatura-alert').html('Non hai compilato tutti i campi');
			$('.alert-danger.candidatura-alert').fadeIn();
			setTimeout(function() { $('.candidatura-alert').fadeOut(); }, 5000);
			return false;
		}
	
		$.post($(this).attr('action'), $(this).serialize(), function(result) {
			if (result.status == 'success') {
				$('.alert-success.candidatura-alert').fadeIn();
				setTimeout(function() { $('.save-alert').fadeOut(); }, 5000);
				$( "#candidatura-form" ).trigger('reset');
			} else {
				$('.alert-danger.candidatura-alert').html(result.error);
				$('.alert-danger.candidatura-alert').fadeIn();
				setTimeout(function() { $('.candidatura-alert').fadeOut(); }, 5000);
			}
		},'json');
		return false;
	});
	
	$('.clear-segnalazione').click( function() {
		$('[data-riga-segnalazione="'+$(this).data('clear')+'"]').val('');
		return false;
	});
	
	$('.insert-candidature a').click( function() {
		var categoria = $(this).data('categoria');
		var campi = $(this).data('candidatura');
		
		var disponibile = 0;
		for (var i=1; i<=3; i++) {	
			var vuoto = true;
			for (var campo in campi) {
				if ($('[name="'+categoria+'-'+i+'-'+campo+'"]').val()) vuoto = false;	
			}
			if (vuoto) {
				disponibile = i;
				break;
			}
		}
		
		if (!disponibile) alert('Non ci sono slot disponibili; devi svuotarne uno se vuoi inserire questa candidatura');
		else {
			for (var campo in campi) {		
				$('[name="'+categoria+'-'+disponibile+'-'+campo+'"]').val(campi[campo]);
			}; 
		}
		return false;
	});
	
	
});

function openAggiungi() {
	if ($('#aggiungi').length > 0) {
		if (window.location.hash == '#aggiungi') {
			$('#aggiungi').slideDown();
			$('#aggiungi-no').slideUp();
		} else {
			$('#aggiungi').slideUp();
			$('#aggiungi-no').slideDown();
		}
	}
}

function stickyIdentity() {
	if ($('#confirm').is(':checked')) $('.row-identita').removeClass('sticky-top');
	else $('.row-identita').addClass('sticky-top');
}

function scroll_to(id) {
  $('html,body').animate({
    scrollTop: $('#'+id).offset().top
  },'slow');
}


function countdown(endDate) {
  let days, hours, minutes, seconds;
  
  endDate = new Date(endDate).getTime();
  
  if (isNaN(endDate)) {
	return;
  }
  
  setInterval(calculate, 1000);
  
  function calculate() {
    let startDate = new Date();
    startDate = startDate.getTime();
    
    let timeRemaining = parseInt((endDate - startDate) / 1000) - 3600; // tolgo un'ora per fuso orario :-)
    
    if (timeRemaining >= 0) {
      days = parseInt(timeRemaining / 86400);
      timeRemaining = (timeRemaining % 86400);
      
      hours = parseInt(timeRemaining / 3600);
      timeRemaining = (timeRemaining % 3600);
      
      minutes = parseInt(timeRemaining / 60);
      timeRemaining = (timeRemaining % 60);
      
      seconds = parseInt(timeRemaining);
      
      let string = ''; let plurale = false; let plurale_set = false;
      if (parseInt(days, 10) > 1) { string += parseInt(days, 10) + ' giorni, '; plurale = true; plurale_set = true; }
      else if (parseInt(days, 10) == 1) { string += 'un giorno, '; plurale = false; plurale_set = true; }

      if (parseInt(hours, 10) > 1) {
      	string += parseInt(hours, 10) + ' ore, ';
      	if (!plurale_set) { plurale = true; plurale_set = true; }
      }
      else if (parseInt(hours, 10) == 1) {
      	string += 'un\'ora, ';
      	if (!plurale_set) { plurale = false; plurale_set = true; }
      }

      if (parseInt(minutes, 10) > 1) {
      	string  += parseInt(minutes, 10) + ' minuti, ';
      	if (!plurale_set) { plurale = true; plurale_set = true; }
      }
      else if (parseInt(minutes, 10) == 1) {
      	string  += 'un minuto, ';
      	if (!plurale_set) { plurale = false; plurale_set = true; }
      }

      if (parseInt(seconds, 10) > 1) {
      	string  += parseInt(seconds, 10) + ' secondi, ';
      	if (!plurale_set) { plurale = true; plurale_set = true; }
      }
      else if (parseInt(seconds, 10) == 1) {
      	string  += 'un secondo, ';
      	if (!plurale_set) { plurale = false; plurale_set = true; }
      }
      
      string = string.replace(/,,+/,',');
      string = string.replace(/, $/,'');
      string = string.replace(/,([^,]+)$/,' e$1');
      string = (plurale ? 'mancano ' : 'manca ')+string;
      if (string == 'manca ') {
	      $('[data-countdown]').html(': il termine è scaduto');
    	  window.location.reload();
    	  return;
      }
      $('[data-countdown]').html(string);
    } else {
      $('[data-countdown]').html(': il termine è scaduto');
      window.location.reload();
      return;
    }
  }
}

$(document).ready(function () {

	$('[data-partecipazione]').click( function() {
		var url = '/api/convention/'+$(this).data('partecipazione');
		if ($(this).data('valore') == 'no' ) url = url + '/no';
		$.get(url, null, function(result) {
			if (result.status == 'success') {
				console.log(result.convention_id);
				console.log(result.partecipato);
				var partecipazione = result.partecipato;
				var nonpartecipazione = result.partecipato == 'yes' ? 'no' : 'yes';
				
				console.log($('[data-partecipazione='+result.convention_id+'][data-valore='+partecipazione+']'));
				$('[data-partecipazione='+result.convention_id+'][data-valore='+partecipazione+']').removeClass('btn-light');
				$('[data-partecipazione='+result.convention_id+'][data-valore='+partecipazione+']').addClass('btn-'+(partecipazione == 'yes' ? 'success' : 'secondary'));
				$('[data-partecipazione='+result.convention_id+'][data-valore='+nonpartecipazione+']').removeClass('btn-'+(partecipazione == 'yes' ? 'secondary' : 'success'));
				$('[data-partecipazione='+result.convention_id+'][data-valore='+nonpartecipazione+']').addClass('btn-light');
			} else {

			}
		},'json');
	});

});