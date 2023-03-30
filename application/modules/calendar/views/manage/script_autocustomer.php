<script>
    $(function() {

        dataCustomer()

        function dataCustomer() {
            data = 'Loading...'

            fetch_dataCustomer()
                .then(resp => {
                    let result = []

                    resp.forEach(function(item, index) {
                        result.push(item.NAME_TH)
                    })

                    $("#tags").autocomplete({
                        source: result
                        // source: ['farmchokchai','zeer']
                    });

                    return result
                })


        }

        async function fetch_dataCustomer() {
            let url = new URL(path('fetch_customer'), domain)
            const response = await fetch(url)
            const result = await response.json()
            return result;
        }


    });
</script>