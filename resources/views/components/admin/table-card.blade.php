<div class="col-12">
  <div class="card mb-4">
    <div class="card-header pb-0 d-flex align-items-center justify-content-between">
      <h6 class="mb-0">
        {{-- 
          SEGURANÇA XSS: 
          - Usar {!! !!} permite renderizar HTML, mas é potencialmente perigoso
          - Todos os valores passados para $title DEVEM usar e() para escape
          - OU usar HtmlSanitizer::sanitize() se precisar de HTML
          - Em caso de dúvida, usar {{ }} com texto simples
        --}}
        @php
          // Se $title contém HTML (tags), sanitizar para prevenir XSS
          // Se for texto simples, usar escape normal
          $isHtml = preg_match('/<[^>]+>/', $title ?? '');
          if ($isHtml) {
              // Sanitizar HTML removendo tags e atributos perigosos
              $sanitized = \App\Helpers\HtmlSanitizer::sanitize($title);
              echo $sanitized;
          } else {
              // Texto simples, usar escape normal
              echo e($title ?? '');
          }
        @endphp
        @isset($count)
          <span class="text-xs text-secondary">({{ $count }})</span>
        @endisset
      </h6>
      @isset($actions)
        <div class="card-actions d-flex align-items-center">
          {{ $actions }}
        </div>
      @endisset
    </div>
    <div class="card-body px-0 pt-0 pb-2">
      <div class="table-responsive p-0">
  <table {{ $attributes->merge([ 'class' => 'table table-striped table-hover align-items-center mb-0 datatable datatable-responsive' ]) }}>
          <thead>
            {{ $head }}
          </thead>
          <tbody>
            {{ $slot }}
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>
