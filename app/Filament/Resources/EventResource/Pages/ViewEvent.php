<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Registration;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            // Toggle Status Action
            Actions\Action::make('toggleStatus')
                ->label(fn () => $this->record->status === 'live' ? 'Set to Draft' : 'Publish')
                ->icon(fn () => $this->record->status === 'live' ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                ->color(fn () => $this->record->status === 'live' ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn () => $this->record->status === 'live' ? 'Set Event to Draft?' : 'Publish Event?')
                ->modalDescription(fn () => $this->record->status === 'live' 
                    ? 'This will hide the event from public view.' 
                    : 'This will make the event visible to users.')
                ->action(function () {
                    $this->record->status = $this->record->status === 'live' ? 'draft' : 'live';
                    $this->record->save();
                    
                    Notification::make()
                        ->title('Status Updated')
                        ->success()
                        ->body("Event status changed to {$this->record->status}.")
                        ->send();
                }),
            
            // Auto-Upgrade from Waitlist Action
            Actions\Action::make('autoUpgrade')
                ->label('Auto-Upgrade from Waitlist')
                ->icon('heroicon-o-arrow-up-circle')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Auto-Upgrade from Waitlist')
                ->modalDescription('This will automatically upgrade the oldest waitlisted user to joined status if there are available spots.')
                ->visible(fn () => $this->record->waitlistedRegistrations()->count() > 0 && !$this->record->isFull())
                ->action(function () {
                    $event = $this->record;
                    $nextWaitlisted = $event->waitlistedRegistrations()
                        ->with('user')
                        ->orderBy('created_at', 'asc')
                        ->first();

                    if ($nextWaitlisted) {
                        $nextWaitlisted->status = 'joined';
                        $nextWaitlisted->save();
                        
                        Notification::make()
                            ->title('User Upgraded')
                            ->success()
                            ->body("{$nextWaitlisted->user->name} has been upgraded from waitlist.")
                            ->send();
                    } else {
                        Notification::make()
                            ->title('No Users Available')
                            ->warning()
                            ->body('No waitlisted users available for upgrade.')
                            ->send();
                    }
                }),
            
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Event Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Event Name'),
                        Infolists\Components\TextEntry::make('start_datetime')
                            ->label('Start Date & Time')
                            ->dateTime('F d, Y - H:i'),
                        Infolists\Components\TextEntry::make('duration')
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
                                return "{$state} minutes";
                            }),
                        Infolists\Components\TextEntry::make('location')
                            ->label('Location'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])->columns(2),

                Infolists\Components\Section::make('Capacity & Registration Stats')
                    ->schema([
                        Infolists\Components\TextEntry::make('capacity')
                            ->label('Event Capacity'),
                        Infolists\Components\TextEntry::make('waitlist_capacity')
                            ->label('Waitlist Capacity'),
                        Infolists\Components\TextEntry::make('joined_count')
                            ->label('Joined Participants')
                            ->getStateUsing(fn ($record) => $record->joinedRegistrations()->count())
                            ->badge()
                            ->color(fn ($state, $record) => $state >= $record->capacity ? 'danger' : 'success'),
                        Infolists\Components\TextEntry::make('waitlist_count')
                            ->label('Waitlisted Participants')
                            ->getStateUsing(fn ($record) => $record->waitlistedRegistrations()->count())
                            ->badge()
                            ->color('warning'),
                        Infolists\Components\TextEntry::make('available_spots')
                            ->label('Available Spots')
                            ->getStateUsing(function ($record) {
                                $joined = $record->joinedRegistrations()->count();
                                return max(0, $record->capacity - $joined);
                            })
                            ->badge()
                            ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                    ])->columns(3),

                Infolists\Components\Section::make('Status')
                    ->schema([
                        Infolists\Components\TextEntry::make('status')
                            ->label('Event Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'live' => 'success',
                            }),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('F d, Y - H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('F d, Y - H:i'),
                    ])->columns(3),
            ]);
    }
}
