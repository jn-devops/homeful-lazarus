<?php

namespace App\Providers\Filament;

use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TomatoPHP\FilamentUsers\Facades\FilamentUser;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->passwordReset()
            ->maxContentWidth(MaxWidth::Full)
            ->unsavedChangesAlerts()
            ->spa()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->sidebarCollapsibleOnDesktop()
            ->topNavigation()
            ->colors([
                'primary' => Color::Zinc,
            ])
            ->navigationGroups([

                NavigationGroup::make()
                    ->label('Dropdowns')
                    ->icon('heroicon-o-numbered-list')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(fn (): string => __('Maintenance'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                GlobalSearchModalPlugin::make()
                    ->maxWidth(MaxWidth::TwoExtraLarge)
                    ->closeButton(enabled: true)
                    ->localStorageMaxItemsAllowed(20)
                    ->RetainRecentIfFavorite(true)
                    ->associateItemsWithTheirGroups()
                    ->placeholder('Type to search...')
                    ->highlighter(false),
                \TomatoPHP\FilamentUsers\FilamentUsersPlugin::make(),
            ]);
    }
//    public function boot()
//    {
//        FilamentUser::registerAction(\Filament\Actions\Action::make('update'));
//        FilamentUser::registerCreateAction(\Filament\Actions\Action::make('update'));
//        FilamentUser::registerEditAction(\Filament\Actions\Action::make('update'));
//        FilamentUser::registerFormInput(\Filament\Forms\Components\TextInput::make('text'));
//        FilamentUser::registerTableAction(\Filament\Tables\Actions\Action::make('update'));
//        FilamentUser::registerTableColumn(\Filament\Tables\Columns\Column::make('text'));
//        FilamentUser::registerTableFilter(\Filament\Tables\Filters\Filter::make('text'));
//    }
}
