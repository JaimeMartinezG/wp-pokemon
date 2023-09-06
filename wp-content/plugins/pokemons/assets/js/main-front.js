jQuery(function($) {
    let items = document.querySelectorAll('.pokemon .show_oldest_version');
    
    items.forEach(item => {
        item.addEventListener( 'click', element => {
            element.preventDefault();
            element.stopPropagation();
            
            const id = item.getAttribute('data-id');

            update_oldest_version.get_data(id);

            return false;
        });
    });
    
    const update_oldest_version = {
        get_data: function(id) {
            const data = {
                action: 'get_oldest_data',
                id: id,
                nonce: wp.urls.nonce
            };
            $.get(wp.urls.admin_ajax, data, response => {
                if(response.success === true) {
                    for(var k in response.data) {
                        document.getElementById(k).innerHTML = "<small>" + response.data[k] + "</small>";
                    }
                }
            });
        }
    };
});