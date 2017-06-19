<strong>Modul view:</strong> {{ $viewName }}<br>

@if ($variables)
    @foreach ($variables as $variable => $value)
        <?php
            $text = $variable;
            if (isset($viewVariables[$variable]) && isset($viewVariables[$variable]['label'])) {
                $text = $viewVariables[$variable]['label'];
            }
        ?>
        <br><strong>{{ $text }}:</strong> {{ $value }}
    @endforeach
@endif