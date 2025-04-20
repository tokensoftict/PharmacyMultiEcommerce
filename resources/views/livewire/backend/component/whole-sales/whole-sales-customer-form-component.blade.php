@script
<script>
    window.removeEventListener('closeWholesalesCustomerModal', closeWholesalesCustomerModal);
    window.removeEventListener('openWholesalesCustomerModal', openWholesalesCustomerModal);
    window.removeEventListener('creatingCustomerOnSuccess', creatingCustomerOnSuccess);

    window.wholesalesCustomerModal = undefined;


  window.generatePassword  = function()
    {
        const password = Math.random().toString(36).slice(-8);
        Livewire.getByName('backend.component.whole-sales.whole-sales-customer-form-component')[0].set('formData.user.password', password, false);
        return false;
    }


    function openWholesalesCustomerModal(e)
    {
        window.wholesalesCustomerModal = bootstrap.Modal.getOrCreateInstance(document.getElementById("wholesales-customer-modal"));

        document.getElementById("wholesales-customer-modal").addEventListener('hidden.bs.modal', event => {
            window.wholesalesCustomerModal = null;
        })

        if( window.wholesalesCustomerModal._isShown === false) {
            window.wholesalesCustomerModal.show();
        }
        window.removeEventListener('closeWholesalesCustomerModal', closeWholesalesCustomerModal);

        window.wholesalesCustomerModal.show();
    }


    function closeWholesalesCustomerModal(e)
    {
        if(e.detail !== null && e.detail[0].hasOwnProperty('status') && e.detail[0].status === true){
           setTimeout(function(){
               window.location.reload();
           }, 1500)
        }
        window.wholesalesCustomerModal.hide();
    }

    function forceCloseWholesalesCustomerModal(e)
    {
        window.wholesalesCustomerModal.hide();
    }


    function creatingCustomerOnSuccess() {
        window.wholesalesCustomerModal.hide();
       setTimeout(function(){
           window.location.reload();
       }, 2000 )
    }

    window.addEventListener('closeWholesalesCustomerModal', closeWholesalesCustomerModal);
    window.addEventListener('openWholesalesCustomerModal', openWholesalesCustomerModal);
    window.addEventListener('creatingCustomerOnSuccess', creatingCustomerOnSuccess);
    window.addEventListener('forceCloseWholesalesCustomerModal', forceCloseWholesalesCustomerModal)
    document.addEventListener('livewire:navigated',function(){
        Livewire.hook('morph.updated', ({ el, component }) => {

            if(el === document.getElementById('select_state_id')){
                $('#select_state_id').select2({
                    dropdownParent: $('#modal-holder'),
                    placeholder:  'Select State',
                    searchInputPlaceholder: 'Search State..'
                });

                $('#select_state_id').val(component.$wire.get('formData.wholesale.state_id'));
                $('#select_state_id').off('change');
                $('#select_state_id').on('change', function (e) {
                    component.$wire.set('formData.wholesale.state_id', $(this).val(), true);
                });
            }

            if(el === document.getElementById('select_town_id')){
                $('#select_town_id').select2({
                    dropdownParent: $('#modal-holder'),
                    placeholder:  'Select Town',
                    searchInputPlaceholder: 'Search Town..'
                });

                $('#select_town_id').val(component.$wire.get('formData.wholesale.town_id'));
                $('#select_town_id').off('change');
                $('#select_town_id').on('change', function (e) {
                    component.$wire.set('formData.wholesale.town_id', $(this).val());
                });
            }

            if(el === document.getElementById('business_type')){
                $('#business_type').select2({
                    dropdownParent: $('#modal-holder'),
                    placeholder:  'Select Business Type',
                    searchInputPlaceholder: 'Search Business Type..'
                });

                $('#business_type').val(component.$wire.get('formData.wholesale.business_type'));
                $('#business_type').off('change');
                $('#business_type').on('change', function (e) {
                    component.$wire.set('formData.wholesale.business_type', $(this).val());
                });
            }


            if(el === document.getElementById('business_group')){
                $('#business_group').select2({
                    dropdownParent: $('#modal-holder'),
                    placeholder:  'Select Business Group',
                    searchInputPlaceholder: 'Search Business Group..'
                });

                $('#business_group').val(component.$wire.get('formData.wholesale.business_group'));
                $('#business_group').off('change');
                $('#business_group').on('change', function (e) {
                    component.$wire.set('formData.wholesale.business_group', $(this).val());
                });
            }

        });
    });
</script>
@endscript

@push('css')
    <link href="{{ asset('backend/admin/lib/select2/css/select2.min.css') }}" rel="stylesheet">
@endpush

@push('js')
    <script src="{{ asset('backend/admin/lib/select2/js/select2.min.js') }}"></script>
@endpush


<div>
    <div  wire:ignore.self class="modal fade effect-scale" id="wholesales-customer-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form method="post" wire:submit.prevent="{{ $this->action === "New" ? 'save()' : 'update()' }}">
                    <div class="modal-body pd-20 pd-sm-30">
                        <a href="#" type="button" class="close pos-absolute t-20 r-20 text-secondary" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </a>

                        <h5 class="tx-18 tx-sm-20 mg-b-20">Create New Wholesales Customer</h5>
                        <p class="tx-13 tx-color-03 mg-b-30">
                            After creating a wholesale customer, an email will be sent to the customer's email address if this is the first time you are creating the customer. However, if this is not the first time, the customer can log in with the previously created account.
                        </p>

                        <div class="col-12" id="modal-holder">
                            <div class="d-sm-flex">
                                <div class="mg-sm-r-30">
                                    <div class="pos-relative d-inline-block mg-b-20">
                                        <div class="avatar avatar-xxl"><span class="avatar-initial rounded-circle bg-gray-700 tx-normal"><ion-icon name="person"></ion-icon></span></div>
                                        <a href="#" class="contact-edit-photo"><i data-feather="edit-2"></i></a>
                                    </div>
                                </div>

                                <div class="flex-fill">
                                    <h4 class="mg-b-3">Account Information</h4>
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" wire:model="formData.user.firstname" id="first_name" placeholder="First Name">
                                        @error("formData.user.firstname") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" wire:model="formData.user.lastname" id="last_name" placeholder="Last Name">
                                        @error("formData.user.lastname") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" wire:model="formData.user.phone" id="last_name" placeholder="Phone Number">
                                        @error("formData.user.phone") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="text" class="form-control" wire:model="formData.user.email" id="email" placeholder="Email Address">
                                        @error("formData.user.email") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group mb-3 mt-2">
                                            <input type="text" class="form-control" id="password" wire:model="formData.user.password" placeholder="Password">
                                            <button class="btn btn-phoenix-success" onclick="return window.generatePassword();" type="button" id="button-addon1">Generate Password</button>
                                        </div>
                                        @error("formData.user.password") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>


                                    <h4 class="mg-b-3">Business Information</h4>
                                    <div class="mb-3">
                                        <label for="business_name" class="form-label">Business Name</label>
                                        <input type="text" class="form-control" wire:model="formData.wholesale.business_name" id="business_name" placeholder="Business Name">
                                        @error("formData.wholesale.business_name") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <x-form-file-manager key="business_cac_certificate" id="business_cac_certificate" model="formData.wholesale.cac_document" placeholder="Select Business CAC Certificate" label="Business CAC Certificate"/>
                                    </div>

                                    <div class="mb-3">
                                        <x-form-file-manager key="business_premises_license" id="business_premises_license" model="formData.wholesale.premises_licence" placeholder="Select Business Premises Licence" label="Business Premises Licence"/>
                                    </div>

                                    <div class="mb-3">
                                        <label for="business_type" class="form-label">Business Type</label>
                                        <x-dropdown-select-menu :options="$data['customer_type_id']['options']"  wire:model="formData.wholesale.customer_type_id" id="business_type" placeholder="Select Business Type"/>
                                        @error("formData.wholesale.customer_type_id") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="business_group" class="form-label">Business Group</label>
                                        <x-dropdown-select-menu placeholder="Select Business Group" wire:model="formData.wholesale.customer_group_id" id="business_group" :options="$data['customer_group_id']['options']"/>
                                        @error("formData.wholesale.customer_group_id") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>


                                    <h4 class="mg-t-20 ">Contact Information</h4>

                                    <div class="mb-3">
                                        <label for="business_phone_number" class="form-label">Business Phone Number</label>
                                        <input type="tel" class="form-control" wire:model="formData.wholesale.phone" id="business_phone_number" placeholder="Business Phone number">
                                        @error("formData.wholesale.phone") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="business_email_address" class="form-label">Business Email Address</label>
                                        <input type="email" class="form-control" wire:model="formData.wholesale.business_email_address" id="business_email_address" placeholder="Business Email address">
                                        @error("formData.wholesale.business_email_address") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="business_address_1"  class="form-label">Business Address 1</label>
                                        <input type="text" class="form-control" wire:model="formData.wholesale.address_1" id="business_address_1" placeholder="Business Address 1">
                                        @error("formData.wholesale.address_1") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="business_address_2"  class="form-label">Business Address 2</label>
                                        <input type="text" id="business_address_2" wire:model="formData.wholesale.address_2" class="form-control" placeholder="Business Address 2">
                                    </div>



                                    <div class="mb-3">
                                        <label for="state"  class="form-label">State</label>
                                        <x-dropdown-select-menu id="select_state_id" placeholder="Select State" wire:model.live="formData.wholesale.state_id" :options="$data['state_id']['options']"/>
                                        @error("formData.wholesale.state_id") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="town"  class="form-label">Town</label>
                                        <x-dropdown-select-menu :options="$data['town_id']['options']" wire:model="formData.wholesale.town_id" id="select_town_id" placeholder="Select Town"/>
                                        @error("formData.wholesale.town_id") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>

                                </div><!-- col -->
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" wire:target="save,update" wire:loading.attr="disabled" class="btn btn-phoenix-primary">
                            <span wire:loading wire:target="save,update" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Save
                        </button>
                        <button type="button"  class="btn btn-phoenix-danger mg-sm-l-5"  onclick="window.dispatchEvent(new CustomEvent('forceCloseWholesalesCustomerModal'))" data-bs-dismiss="modal">Cancel</button>
                    </div>
                    <!-- modal-footer -->
                </form>
            </div><!-- modal-content -->
        </div><!-- modal-dialog -->
    </div><!-- modal -->
</div>
