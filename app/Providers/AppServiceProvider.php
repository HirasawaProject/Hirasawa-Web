<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive("pipeimplode", function (array $array) {
            return "<?php echo implode('|', $array); ?>";
        });

        Blade::directive("keyvaluepipeimplode", function ($array) {
            return <<<HEREDOC
            <?php
                \$resultString = '';
                // Loop through the array and concatenate the keys and values
                foreach ($array as \$key => \$value) {
                    // Append the key-value pair to the result string
                    \$resultString .= "\$key:\$value|";
                }

                // Remove the trailing '|' character from the end of the string
                \$resultString = rtrim(\$resultString, '|');

                echo \$resultString;
            ?>
            HEREDOC;
        });
    }
}
