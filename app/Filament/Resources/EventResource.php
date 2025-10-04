<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Events';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Event Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Event Name'),

                        Forms\Components\DateTimePicker::make('start_datetime')
                            ->required()
                            ->native(false)
                            ->label('Start Date & Time')
                            ->seconds(false)
                            ->minDate(now()),

                        Forms\Components\TextInput::make('duration')
                            ->required()
                            ->numeric()
                            ->default(120)
                            ->suffix('minutes')
                            ->label('Duration')
                            ->minValue(1),

                        Forms\Components\TextInput::make('location')
                            ->required()
                            ->maxLength(255)
                            ->label('Location'),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(4)
                            ->label('Description'),
                    ])->columns(2),

                Forms\Components\Section::make('Capacity Settings')
                    ->schema([
                        Forms\Components\TextInput::make('capacity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->label('Event Capacity')
                            ->helperText('Maximum number of participants who can join this event'),

                        Forms\Components\TextInput::make('waitlist_capacity')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->label('Waitlist Capacity')
                            ->helperText('Maximum number of participants who can be on the waitlist'),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'draft' => 'Draft',
                                'live' => 'Live',
                            ])
                            ->default('draft')
                            ->label('Event Status')
                            ->helperText('Only live events are visible to users'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Event Name'),

                Tables\Columns\TextColumn::make('start_datetime')
                    ->dateTime('M d, Y - H:i')
                    ->sortable()
                    ->label('Start Date & Time'),

                Tables\Columns\TextColumn::make('duration')
                    ->sortable()
                    ->label('Duration')
                    ->formatStateUsing(function ($state) {
                        if ($state >= 60) {
                            $hours = floor($state / 60);
                            $minutes = $state % 60;
                            if ($minutes > 0) {
                                return "{$hours}h {$minutes}m";
                            }
                            return "{$hours}h";
                        }
                        return "{$state}m";
                    }),

                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->label('Location')
                    ->limit(30),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'live',
                    ])
                    ->label('Status'),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacity')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('joined_count')
                    ->label('Joined')
                    ->alignCenter()
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->joinedRegistrations()->count())
                    ->color(fn ($state, $record) => $state >= $record->capacity ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('waitlist_count')
                    ->label('Waitlist')
                    ->alignCenter()
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->waitlistedRegistrations()->count())
                    ->color('warning'),

                Tables\Columns\TextColumn::make('available_spots')
                    ->label('Available')
                    ->alignCenter()
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $joined = $record->joinedRegistrations()->count();
                        return max(0, $record->capacity - $joined);
                    })
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'live' => 'Live',
                    ])
                    ->label('Event Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                // Toggle Status Action
                Tables\Actions\Action::make('toggleStatus')
                    ->label(fn (Event $record) => $record->status === 'live' ? 'Set to Draft' : 'Publish')
                    ->icon(fn (Event $record) => $record->status === 'live' ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn (Event $record) => $record->status === 'live' ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Event $record) => $record->status === 'live' ? 'Set Event to Draft?' : 'Publish Event?')
                    ->modalDescription(fn (Event $record) => $record->status === 'live' 
                        ? 'This will hide the event from public view.' 
                        : 'This will make the event visible to users.')
                    ->action(function (Event $record) {
                        $record->status = $record->status === 'live' ? 'draft' : 'live';
                        $record->save();
                        
                        Notification::make()
                            ->title('Status Updated')
                            ->success()
                            ->body("Event status changed to {$record->status}.")
                            ->send();
                    }),
                    
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_datetime', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RegistrationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'view' => Pages\ViewEvent::route('/{record}'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }

    /**
     * Show all events (draft and published) to admins
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
