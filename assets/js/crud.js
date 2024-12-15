$(document).ready(function() {

// reservation chambre user 
$('#reserv_chambre_typeChambre').on('change', function () {
    const type = $(this).val();
    const foyerId = $('#foyerId').val();
    const chambreSelect = $('#reserv_chambre_chambre');
    console.log(foyerId)
    // Clear existing options and show loading
    chambreSelect.html('<option value="">Loading...</option>');

    // Fetch chambers based on type
    $.ajax({
        url: `/foyers/${foyerId}/chambres`,
        method: 'GET',
        data: { type: type },
        dataType: 'json',
        success: function (data) {
            chambreSelect.empty();
            if (data.error) {
                alert(data.error);
                return;
            }

            // Populate the select dropdown with new options
            $.each(data, function (index, chambre) {
                chambreSelect.append(new Option(chambre.nom, chambre.id));
            });
        },
        error: function (xhr, status, error) {
            console.error('Error fetching chambers:', error);
            chambreSelect.html('<option value="">Error loading options</option>');
        }
    });
});

//verify reservation Admin
$(document).on('click', '[id^="verifyReservation-"]', function (e) {
    e.preventDefault();  // Prevent the default action

    var reservationId = $(this).data('reservation-id'); // Get the reservation ID

    // Trigger SweetAlert confirmation dialog
    Swal.fire({
        title: 'Vérifier la réservation',
        text: "Voulez-vous vérifier cette réservation ?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Oui, vérifier',
        cancelButtonText: 'Non, annuler',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Make an AJAX request to the Symfony controller to update the reservation
            $.ajax({
                url: '/dashboared/reservation/verify-reservation',  // Adjust the URL as needed
                method: 'POST',
                data: {
                    reservationId: reservationId,  // Send the reservation ID
                    _token: '{{ csrf_token("verify_reservation") }}'  // CSRF token for security
                },
                success: function (data) {
                    if (data.success) {
                        Swal.fire('Réservation vérifiée', 'La réservation a été vérifiée avec succès.', 'success');
                        location.reload();
                    } else {
                        Swal.fire('Erreur', data.message || 'Une erreur est survenue lors de la vérification de la réservation.', 'error');
                    }
                },
                error: function () {
                  //  Swal.fire('Erreur', 'Impossible de vérifier la réservation. Essayez à nouveau.', 'error');
                  var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Impossible de vérifier la réservation. Essayez à nouveau.';
                    Swal.fire('Erreur', errorMessage, 'error');
                }
            });
        }
    });
});

     // Function to fetch and display notifications
     function fetchNotifications() {
        $.ajax({
            url: '/dashboared/api/notifications',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                const notificationCount = $('#notification-count');
                const notificationList = $('#notification-list');

                // Update notification count
                if (data.length > 0) {
                    notificationCount.text(data.length);
                    notificationCount.removeClass('d-none');
                } else {
                    notificationCount.addClass('d-none');
                }

                // Clear previous notifications
                notificationList.empty();

                // Add each notification to the list
                $.each(data, function(index, notification) {
                    const notificationItem = $('<li>').addClass('dropdown-item');
                    const notificationLink = $('<a>').attr('href', '/dashboared/reservation/')
                        .attr('class', 'd-flex w-100')
                        .html(`
                             <div class="d-flex w-100">
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">${notification.type}</span>
                                    <small class="text-muted">${notification.message}</small>
                                </div>
                                <button class="btn btn-sm btn-link text-muted mark-as-read" data-notification-id="${notification.id}">X</button>
                             </div>
                             `);
                    notificationItem.append(notificationLink);
                    notificationList.append(notificationItem);
                    notificationItem.find('.mark-as-read').on('click', function(e) {
                        e.preventDefault();  // Prevent default action (link navigation)

                        const notificationId = $(this).data('notification-id');
                        
                        // Send AJAX request to mark the notification as read
                        $.ajax({
                            url: '/dashboared/api/mark-as-read-notification/' + notificationId,
                            method: 'POST',
                            success: function(response) {
                                // Remove the notification item from the list
                                notificationItem.remove();
                                console.log('Notification marked as read:', response.success);
                            },
                            error: function(error) {
                                console.error('Error marking notification as read:', error);
                            }
                        });
                    });
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching notifications:', error);
            }
        });
    }

    fetchNotifications();
    setInterval(fetchNotifications, 30000);

    $('#notification-icon').on('click', function() {
        const notificationDropdown = $('#notification-list');

        notificationDropdown.toggleClass('show');

        if (!notificationDropdown.hasClass('show')) {
            fetchNotifications(); 
        }
    });


})