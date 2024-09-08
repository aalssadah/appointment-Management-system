<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ReservationResource;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{

    public  Model | string | null $model = Reservation::class;

    private string $reservation;


    public function fetchEvents(array $fetchInfo): array
    {
//        dd(FilamentFullCalendarPlugin::make()->config(['weekends=>true'])->getConfig());
//        dd(FilamentFullCalendarPlugin::get()->getConfig());
//        dd($fetchInfo);
        return Reservation::query()
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
//            ->where('starts_at', '>=','2024-09-01 11:59:57')// $fetchInfo['start'])
//            ->where('ends_at', '<=','2024-09-08 11:59:57')//$fetchInfo['end'])
            ->get()
            ->map(
                fn (Reservation $event) => [
                    'id'=>$event->id,
                    'title' => $event->title,
                    'color'=>$event->color,
                    'start' => $event->starts_at,
                    'end' => $event->ends_at,
                ]
            )
            ->all();
    }

    public function getFormSchema(): array
    {
        return [
            Section::make()->schema([

            TextInput::make('title'),
            ColorPicker::make('color'),
            Textarea::make('description')->columnSpanFull(),

            Grid::make()
                ->schema([
                    DateTimePicker::make('starts_at'),

                    DateTimePicker::make('ends_at'),
                ]),
            ])->columns(2),
        ];
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()->label('Add Reservation'),
            Action::make('Alaa')
                ->url(fn (): string => route('filament.admin.resources.reservations.create'))
                ->icon('heroicon-m-pencil-square')
        ];
    }

    protected function modalActions(): array
    {
        return [
            CreateAction::make()
            ->mountUsing( function ( Form $form, array $arguments) {
                $form->fill([
                    'starts_at' => $arguments['start'] ?? null,
                    'ends_at' => $arguments['end'] ?? null
                ]);
                }
            ),

            EditAction::make()
                ->mountUsing(
                    function (Reservation $record, Form $form, array $arguments) {
                        $form->fill([
                            'title' => $record->title,
                            'color'=>$record->color,
                            'description'=>$record->description,
                            'starts_at' => $arguments['event']['start'] ?? $record->starts_at,
                            'ends_at' => $arguments['event']['end'] ?? $record->ends_at
                        ]);
                    }
                ),
            DeleteAction::make(),
        ];
    }

    public function eventDidMount(): string
    {
        return <<<JS
        function({ event, timeText, isStart, isEnd, isMirror, isPast, isFuture, isToday, el, view }){
            el.setAttribute("x-tooltip", "tooltip");
            console.log(event);
            el.setAttribute("x-data", "{ tooltip: '"+event.title+"' }");
        }
    JS;
    }
}
