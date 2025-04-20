<?php

use App\Mail\Customer\AdministratorPasswordResetEmail;
use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use App\Models\SalesRepresentative;
use Illuminate\Support\Facades\Session;
use App\Models\AppUser;
use App\Classes\AppLists;
use App\Models\User;

new class extends Component {
    public string $salesRep = "";

    public string $successMessage = "";

    public string $password;

    public bool $sendPasswordToEmail = false;

    public User $user;

    public function openModal(): void
    {
        $this->dispatch('openResetPasswordModal');
    }


    public function performResetPassword()
    {
        $this->validate(['password' => 'required']);
        return DB::transaction(function () {
            $this->user->password = bcrypt($this->password);
            $this->user->save();

            if ($this->sendPasswordToEmail) {
                Mail::to($this->user->email)->send(new AdministratorPasswordResetEmail($this->user, $this->password));
                Session::flash('status', "Password has been reset and send to user's mail box successfully!  &#128513;");
            } else {
                Session::flash('status', "Password has been reset successfully!  &#128513;");
            }

            $this->dispatch("closeAddCustomerModal", ['status' => true]);

            return true;
        });
    }

}
?>


@script
<script>
    const salesModal = bootstrap.Modal.getOrCreateInstance(document.getElementById("reset-password-component"));

    window.generatePassword = function () {
        const password = Math.random().toString(36).slice(-8);
        Livewire.getByName('backend.component.resetpassword')[0].set('password', password, false);
        return false;
    }

    function openResetPasswordModal(e) {
        //let detail = e.detail[0];
        //let component = window.Livewire.find('{{ $this->getId() }}');
        salesModal.show();
    }

    function closeResetPasswordModal(e) {
        if (e.detail !== null && e.detail[0].hasOwnProperty('status') && e.detail[0].status === true) {
            setTimeout(function () {
                salesModal.hide();
                window.location.reload();
            }, 3500)
        }
    }

    window.addEventListener('closeResetPasswordModal', closeResetPasswordModal);
    window.addEventListener('openResetPasswordModal', openResetPasswordModal);

</script>
@endscript


<div>
    <div wire:ignore.self class="modal fade" id="reset-password-component" tabindex="-1" role="dialog"
         aria-hidden="true">
        <form method="post" wire:submit.prevent="performResetPassword">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        Reset Password
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('closeResetPasswordModal'))"
                                class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12" id="modal-holder">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group mt-2">
                                        <input type="text" class="form-control" id="password" wire:model="password"
                                               placeholder="Password">
                                        <button class="btn btn-phoenix-success"
                                                onclick="return window.generatePassword();" type="button"
                                                id="button-addon1">Generate Password
                                        </button>
                                    </div>
                                    @error("password") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                </div>

                                <div class="form-check form-switch mt-5">
                                    <input class="form-check-input" id="flexSwitchCheckDefault"
                                           wire:model="sendPasswordToEmail" type="checkbox">
                                    <label class="form-check-label" for="flexSwitchCheckDefault">Send password to user's
                                        mailbox</label>
                                </div>
                            </div>
                            @if (session()->has('status'))
                                <div class="col-12">
                                    <div class="alert alert-success" role="alert">{!!  \session('status') !!}</div>
                                </div>
                            @endif
                        </div>
                        <br/>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" wire:target="performResetPassword" wire:loading.attr="disabled"
                                class="btn btn-phoenix-primary">
                            <span wire:loading wire:target="performResetPassword"
                                  class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Reset Password
                        </button>
                        <button type="button" wire:target="performResetPassword" wire:loading.attr="disabled"
                                class="btn btn-phoenix-danger"
                                onclick="window.dispatchEvent(new CustomEvent('closeResetPasswordModal'))"
                                data-dismiss="modal" aria-label="Close">Cancel
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


