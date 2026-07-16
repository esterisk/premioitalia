@php
    $color = match ($mailingStatus) {
        \App\Enums\MailingStatusEnum::Idle     => 'gray',
        \App\Enums\MailingStatusEnum::Waiting  => 'info',
        \App\Enums\MailingStatusEnum::Sending  => 'warning',
        \App\Enums\MailingStatusEnum::Checking => 'primary',
        \App\Enums\MailingStatusEnum::Problem  => 'danger',
        default                                => 'gray',
    };
@endphp

<x-filament-widgets::widget>
    <x-filament::section heading="Mailing">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <x-filament::badge :color="$color" size="lg">
                    {{ $mailingStatus?->label() ?? 'N/D' }}
                </x-filament::badge>
                {{ \App\Models\User::isValid()->where('last_invitation', \App\Models\Annata::corrente()->mailingTag())->count() }} inviti spediti
            </div>

            <div class="flex items-center gap-3">
                {{ $this->sendReminderAction }}

                @if(in_array($mailingStatus, [\App\Enums\MailingStatusEnum::Idle, \App\Enums\MailingStatusEnum::Problem], strict: true))
                    {{ $this->startMailingAction }}
                @elseif($mailingStatus === \App\Enums\MailingStatusEnum::Sending)
                    {{ $this->stopMailingAction }}
                @endif
            </div>
        </div>

        <x-filament-actions::modals />
    </x-filament::section>
</x-filament-widgets::widget>
