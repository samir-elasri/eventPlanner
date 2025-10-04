<?php

namespace App\Filament\Resources\EventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Registrations';

    protected static ?string $recordTitleAttribute = 'id';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'joined',
                        'warning' => 'waitlist',
                    ])
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('joined_at')
                    ->label('Joined At')
                    ->dateTime('M d, Y - H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered At')
                    ->dateTime('M d, Y - H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'joined' => 'Joined',
                        'waitlist' => 'Waitlist',
                    ])
                    ->label('Registration Status'),
            ])
            ->headerActions([
                // Optional: Add create action if needed
            ])
            ->actions([
                // Upgrade from Waitlist Action
                Tables\Actions\Action::make('upgrade')
                    ->label('Upgrade to Joined')
                    ->icon('heroicon-o-arrow-up')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'waitlist')
                    ->requiresConfirmation()
                    ->modalHeading('Upgrade User from Waitlist')
                    ->modalDescription(fn ($record) => "Are you sure you want to upgrade {$record->user->name} from waitlist to joined?")
                    ->action(function ($record) {
                        $event = $this->getOwnerRecord();
                        
                        // Check if event is full
                        if ($event->isFull()) {
                            Notification::make()
                                ->title('Event is Full')
                                ->danger()
                                ->body('Cannot upgrade user. Event has reached maximum capacity.')
                                ->send();
                            return;
                        }
                        
                        // Check for overlapping events
                        $overlappingEvents = \App\Models\Event::getOverlappingEventsForUser($record->user_id, $event);
                        
                        if (!empty($overlappingEvents)) {
                            $overlappingEventNames = [];
                            foreach ($overlappingEvents as $e) {
                                $overlappingEventNames[] = $e->name;
                            }
                            
                            Notification::make()
                                ->title('Schedule Conflict')
                                ->danger()
                                ->body('Cannot upgrade user due to overlapping events: ' . implode(', ', $overlappingEventNames))
                                ->send();
                            return;
                        }
                        
                        // Upgrade the user
                        $record->status = 'joined';
                        $record->save();
                        
                        Notification::make()
                            ->title('User Upgraded')
                            ->success()
                            ->body("{$record->user->name} has been upgraded from waitlist to joined.")
                            ->send();
                    }),
                    
                // Move to Waitlist Action (for joined users)
                Tables\Actions\Action::make('moveToWaitlist')
                    ->label('Move to Waitlist')
                    ->icon('heroicon-o-arrow-down')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'joined')
                    ->requiresConfirmation()
                    ->modalHeading('Move User to Waitlist')
                    ->modalDescription(fn ($record) => "Are you sure you want to move {$record->user->name} from joined to waitlist?")
                    ->action(function ($record) {
                        $event = $this->getOwnerRecord();
                        
                        // Check if waitlist is full
                        if ($event->isWaitlistFull()) {
                            Notification::make()
                                ->title('Waitlist is Full')
                                ->danger()
                                ->body('Cannot move user to waitlist. Waitlist has reached maximum capacity.')
                                ->send();
                            return;
                        }
                        
                        $record->status = 'waitlist';
                        $record->save();
                        
                        Notification::make()
                            ->title('User Moved to Waitlist')
                            ->success()
                            ->body("{$record->user->name} has been moved to waitlist.")
                            ->send();
                    }),
                    
                Tables\Actions\DeleteAction::make()
                    ->label('Remove')
                    ->modalHeading('Remove Registration')
                    ->modalDescription(fn ($record) => "Are you sure you want to remove {$record->user->name}'s registration?"),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'asc')
            ->poll('10s'); // Auto-refresh every 10 seconds
    }
}
