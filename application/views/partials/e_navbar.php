           <!-- Topbar Start -->
           <div class="navbar-custom">
               <ul class="list-unstyled topnav-menu float-right mb-0">
                   <li class="dropdown notification-list">
                       <a class="nav-link dropdown-toggle  waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                           <i class="dripicons-bell noti-icon"></i>
                           <!-- <span class="badge badge-pink rounded-circle noti-icon-badge">4</span> -->
                       </a>
                       <div class="dropdown-menu dropdown-menu-right dropdown-lg">

                           <div class="dropdown-header noti-title">
                               <h5 class="text-overflow m-0"><span class="float-right">
                                       <span class="badge badge-danger float-right">5</span>
                                   </span>Notification</h5>
                           </div>

                           <div class="slimscroll noti-scroll">

                               <a href="javascript:void(0);" class="dropdown-item notify-item">
                                   <div class="notify-icon bg-success"><i class="mdi mdi-comment-account-outline"></i></div>
                                   <p class="notify-details">Robert S. Taylor commented on Admin<small class="text-muted">1 min ago</small></p>
                               </a>

                               <!-- item-->
                               <a href="javascript:void(0);" class="dropdown-item notify-item">
                                   <div class="notify-icon bg-primary">
                                       <i class="mdi mdi-settings-outline"></i>
                                   </div>
                                   <p class="notify-details">New settings
                                       <small class="text-muted">There are new settings available</small>
                                   </p>
                               </a>

                               <!-- item-->
                               <a href="javascript:void(0);" class="dropdown-item notify-item">
                                   <div class="notify-icon bg-warning">
                                       <i class="mdi mdi-bell-outline"></i>
                                   </div>
                                   <p class="notify-details">Updates
                                       <small class="text-muted">There are 2 new updates available</small>
                                   </p>
                               </a>

                               <!-- item-->
                               <a href="javascript:void(0);" class="dropdown-item notify-item">
                                   <div class="notify-icon">
                                       <img src="<?= base_url('asset/images/users/avatar-4.jpg') ?>" class="img-fluid rounded-circle" alt="" />
                                   </div>
                                   <p class="notify-details">Karen Robinson</p>
                                   <p class="text-muted mb-0 user-msg">
                                       <small>Wow ! this admin looks good and awesome design</small>
                                   </p>
                               </a>

                               <!-- item-->
                               <a href="javascript:void(0);" class="dropdown-item notify-item">
                                   <div class="notify-icon bg-danger">
                                       <i class="mdi mdi-account-plus"></i>
                                   </div>
                                   <p class="notify-details">New user
                                       <small class="text-muted">You have 10 unread messages</small>
                                   </p>
                               </a>

                               <!-- item-->
                               <a href="javascript:void(0);" class="dropdown-item notify-item">
                                   <div class="notify-icon bg-info">
                                       <i class="mdi mdi-comment-account-outline"></i>
                                   </div>
                                   <p class="notify-details">Caleb Flakelar commented on Admin
                                       <small class="text-muted">4 days ago</small>
                                   </p>
                               </a>

                               <!-- item-->
                               <a href="javascript:void(0);" class="dropdown-item notify-item">
                                   <div class="notify-icon bg-secondary">
                                       <i class="mdi mdi-heart"></i>
                                   </div>
                                   <p class="notify-details">Carlos Crouch liked
                                       <b>Admin</b>
                                       <small class="text-muted">13 days ago</small>
                                   </p>
                               </a>
                           </div>

                           <!-- All-->
                           <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item notify-all">
                               View all
                               <i class="fi-arrow-right"></i>
                           </a>

                       </div>
                   </li>

                   <li class="dropdown notification-list">
                       <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                           <!-- <img src="<?= base_url('asset/images/users/avatar-1.jpg') ?>" alt="user-image" class="rounded-circle"> -->
                           <img src="<?= base_url('asset/images/users/avatar6@2x.png') ?>" alt="user-image" class="rounded-circle">
                           <span class="pro-user-name ml-1">
                               <?php
                                echo $this->session->userdata('user_name');
                                ?> <i class="mdi mdi-chevron-down"></i>
                           </span>
                       </a>
                       <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                           <!-- item-->
                           <div class="dropdown-header noti-title">
                               <h6 class="text-overflow m-0">Welcome</h6>
                           </div>

                           <!-- item-->
                           <a href="<?= site_url('profile/ctl_profile/') ?>" class="dropdown-item notify-item">
                               <i class="fe-user"></i>
                               <span>Profile</span>
                           </a>


                           <div class="dropdown-divider"></div>

                           <!-- item-->
                           <a href="<?= site_url('login/ctl_logout/') ?>" class="dropdown-item notify-item">
                               <i class="fe-log-out"></i>
                               <span>Logout</span>
                           </a>

                       </div>
                   </li>
               </ul>

               <!-- LOGO -->
               <style>
                   .logo-box {
                       background-color: #bdc2c5;
                   }

                   .logo-box span {
                       color: #FFF
                   }
               </style>
               <div class="logo-box">
                   <a href="index.html" class="logo text-center">
                       <span class="logo-lg">
                           <!-- <img src="<?= base_url('asset/images/logo-light.png') ?>" alt="" height="25"> -->
                           <!-- <span class="logo-lg-text-light">E-leave.Chokchai</span> -->
                           <span style="font-size: 22px;">Backend Tour</span>
                       </span>
                       <span class="logo-sm">
                           <!-- <img src="<?= base_url('asset/images/logo-sm.png') ?>" alt="" height="28"> -->
                       </span>
                   </a>
               </div>

               <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                   <li>
                       <button class="button-menu-mobile waves-effect waves-light">
                           <i class="fe-menu"></i>
                       </button>
                   </li>
               </ul>


           </div>
           <!-- end Topbar -->

           <script>
               let domain = window.location.origin
               let table_toolbar_name = 'toolbar'
               let table_toolbar = '#datatable_wrapper div.' + table_toolbar_name
               let datatable_dom = "<'row'<'col-md-4 btn-sm'B><'col-md-4 btn-sm " + table_toolbar_name + " text-center'><'col-md-4 'f>>" +
                   "<'row'<'col-sm-12 small'tr>>" +
                   "<'row'<'col-sm-4 small'i><'col-sm-4 d-flex justify-content-center small'l><'col-sm-4 small'p>>"
               let datatable_button = [
                   'print',
                   {
                       extend: 'collection',
                       text: 'Export',
                       buttons: ['excel', 'pdf', 'copy'],
                       fade: true
                   },
                   {
                       extend: 'collection',
                       text: 'Tool',
                       buttons: ['columnsToggle', 'colvisRestore'],
                       fade: true
                   },
                   {
                       text: '<i class="fas fa-redo-alt"></i>',
                       className: '',
                       titleAttr: 'reload',
                       action: function(e, dt, node, config) {
                           //
                           //	API reload(callback,resetPaging [default true,false])
                           //
                           dt.ajax.reload();
                           // dt.ajax.reload(null, false);
                       }
                   },
               ]

               let loading = `<div class="sk-circle loading">
                                        <div class="sk-circle1 sk-child"></div>
                                        <div class="sk-circle2 sk-child"></div>
                                        <div class="sk-circle3 sk-child"></div>
                                        <div class="sk-circle4 sk-child"></div>
                                        <div class="sk-circle5 sk-child"></div>
                                        <div class="sk-circle6 sk-child"></div>
                                        <div class="sk-circle7 sk-child"></div>
                                        <div class="sk-circle8 sk-child"></div>
                                        <div class="sk-circle9 sk-child"></div>
                                        <div class="sk-circle10 sk-child"></div>
                                        <div class="sk-circle11 sk-child"></div>
                                        <div class="sk-circle12 sk-child"></div>
                                    </div>`

               let swal_autoClose = 2000
               let swal_confirmButton = '#64c5b1'
               let swal_cancelButton = '#f96a74'
               let swal_confirmText = 'ยืนยัน'
               let swal_cancelText = 'ยกเลิก'

               function swal_setConfirm(title = 'ยืนยันการลบ', text = 'คุณต้องการลบข้อมูลนี้') {
                   return {
                       title: title,
                       text: text,
                       type: 'question',
                       showCancelButton: true,
                       confirmButtonColor: swal_confirmButton,
                       cancelButtonColor: swal_cancelButton,
                       confirmButtonText: swal_confirmText,
                       cancelButtonText: swal_cancelText
                   }
               }

               function swalalert(type = 'success', text = 'ทำรายการสำเร็จ', optional = {
                   auto: true
               }) {

                   let timeclose_total = swal_autoClose
                   let title = 'สำเร็จ'

                   if (optional.auto == false) {
                       timeclose_total = null
                   }

                   if (type == 'warning') {
                       title = 'แจ้งเตือน'
                   }

                   if (type == 'error') {
                       title = 'ไม่สำเร็จ'
                   }

                   Swal.fire({
                       type: type,
                       title: title,
                       text: text,
                       timer: timeclose_total,
                   })
               }
               //	convert thai date
               //	@param	date	@date = date yyyy-mm-dd
               //	@param	typereturn	@text = [date , datetime]
               //	return datetime TH
               //
               function toThaiDateTimeString(dateset, typereturn) {
                   let monthNames = [
                       "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน",
                       "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม.",
                       "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                   ];

                   console.log(dateset)
                   let date = new Date(dateset)
                   let year = date.getFullYear() + 543;
                   console.log(year, date)
                   let month = monthNames[date.getMonth()];
                   let numOfDay = date.getDate();
                   // console.log(date + "--" + typereturn);
                   let hour = date.getHours().toString().padStart(2, "0");
                   let minutes = date.getMinutes().toString().padStart(2, "0");
                   let second = date.getSeconds().toString().padStart(2, "0");

                   switch (typereturn) {
                       case 'datetime':
                           return `${numOfDay} ${month} ${year} ` +
                               `${hour}:${minutes}:${second} น.`;
                           break;
                       case 'date':
                           return `${numOfDay} ${month} ${year} `;
                           break;
                       default:
                           return `${numOfDay} ${month} ${year} ` +
                               `${hour}:${minutes}:${second} น.`;
                           break;
                   }

               }

               //
               // data table
               function addTableToolbar(dom = null) {
                   if (dom) {

                       if ($(document).find(table_toolbar).length) {
                           $(table_toolbar).prepend(dom)
                       }
                   }
               }

               //
               // data system update
               /* updateSystem()

               function updateSystem() {
                   update_doc_waite()
                       .then(res => res.json())
                       .then((resp) => {
                           if (resp.total > 0) {
                               document.querySelector('.total_doc_waite').innerHTML = resp.total
                               document.querySelector('.total_doc_waite').classList.remove("d-none")
                           }else{
                                document.querySelector('.total_doc_waite').classList.add("d-none")
                           }
                       })
               }

               async function update_doc_waite() {
                   let url = new URL('realdata/ctl_data/get_doc_waite', domain);
                   let result = await fetch(url);

                   return result
               } */

               // return path
               function path(name = null) {
                   let pathname = window.location.pathname;
                   if (name) {
                       pathname = pathname + '/' + name
                   }

                   return pathname
               }
           </script>