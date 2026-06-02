<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('firstname')
                    ->required(),
                TextInput::make('lastname')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('signature'),
                TextInput::make('email_verified')
                    ->email()
                    ->required()
                    ->numeric(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                TextInput::make('username'),
                TextInput::make('token'),
                DatePicker::make('token_expire'),
                TextInput::make('user_status')
                    ->required()
                    ->numeric()
                    ->default(1),
                TextInput::make('status_detail'),
                Textarea::make('motivi')
                    ->columnSpanFull(),
                TextInput::make('ultimo_voto')
                    ->numeric()
                    ->default(0),
                TextInput::make('admin')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('last_invitation'),
                DateTimePicker::make('invitation_sent'),
                TextInput::make('email_errors')
                    ->email()
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('last_email_error')
                    ->email(),
                TextInput::make('invitation_open'),
            ]);
    }
}
