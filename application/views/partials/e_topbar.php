<!-- start page title -->

<div id="topbar" class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);"></a></li>
                    <li class="breadcrumb-item active"></li>
                </ol>
            </div>
            <h4 class="page-title"></h4>
        </div>
    </div>
</div>
<!-- end page title -->

<script>
$(document).ready(function(){
    let menu = $('.nav-second-level li.mm-active')
    let main = $('.metismenu > li.mm-active span:first')

    if(menu.text()){
        document.getElementsByClassName('breadcrumb-item')[0].innerHTML = 'Backend'
        document.getElementsByClassName('breadcrumb-item')[1].innerHTML = main.text()
        document.getElementsByClassName('breadcrumb-item')[2].innerHTML = menu.text()
        document.getElementsByClassName('page-title')[0].innerHTML = menu.text()
    }
})


    document.getElementsByClassName('breadcrumb-item')[0].innerHTML = '.'
    document.getElementsByClassName('breadcrumb-item')[1].innerHTML = '.'
    document.getElementsByClassName('breadcrumb-item')[2].innerHTML = '.'
    document.getElementsByClassName('page-title')[0].innerHTML = '.'

</script>