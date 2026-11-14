<script>
    function cbEsc(v) {
        return $('<div>').text(v == null ? '' : String(v)).html();
    }

    function cbLoadChat() {
        $('#chat_refresh').prop('disabled', true).html("{{ __('manage.loading') }}");
        $('#live_chat tbody').empty();

        $.ajax({
            type: 'GET',
            url: '{{ route('admin.chat.postLogs') }}',
            dataType: 'json',
            success: function (r) {
                if (!Array.isArray(r) || r.length === 0) {
                    $('#live_chat tbody').append("<tr><td colspan='6' class='py-4 text-center opacity-70'>Belum ada log chat yang dapat dibaca.</td></tr>");
                    return;
                }
                for (let i = r.length - 1; i >= 0; i--) {
                    const name = r[i].character_name ? cbEsc(r[i].character_name) : '—';
                    const uid = r[i].uid ? cbEsc(r[i].uid) : '—';
                    const target = r[i].dest ? cbEsc(r[i].dest) : '—';
                    const rowColor = r[i].row_color ? cbEsc(r[i].row_color) : '';
                    $('#live_chat tbody').append(
                        "<tr class='border-b dark:border-primary-light " + rowColor + "'>" +
                        "<td class='whitespace-nowrap'>" + cbEsc(r[i].time || r[i].date || '—') + "</td>" +
                        "<td>" + name + "</td>" +
                        "<td>" + uid + "</td>" +
                        "<td>" + cbEsc(r[i].channel || '—') + "</td>" +
                        "<td>" + target + "</td>" +
                        "<td class='break-words'>" + cbEsc(r[i].msg || '') + "</td>" +
                        "</tr>"
                    );
                }
            },
            error: function () {
                $('#live_chat tbody').append("<tr><td colspan='6' class='py-4 text-center text-red-400'>Gagal membaca live chat. Periksa lokasi world2.chat dan permission.</td></tr>");
            },
            complete: function () {
                $('#chat_refresh').prop('disabled', false).html("<span class='icon-refresh'></span> {{ __('manage.buttons.refresh') }}");
            }
        });
    }

    $(document).ready(function () {
        cbLoadChat();
        $('#chat_refresh').click(function () { cbLoadChat(); });
    });
</script>
