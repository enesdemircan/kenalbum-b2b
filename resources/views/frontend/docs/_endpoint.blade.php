@php
  $method = $method ?? 'GET';
  $path = $path ?? '/';
  $auth = $auth ?? true;
  $desc = $desc ?? '';
  $request = $request ?? null;
  $response = $response ?? null;
  $name = $name ?? null;
  $id = $id ?? null;

  $methodColors = ['GET' => 'm-get', 'POST' => 'm-post', 'PUT' => 'm-put', 'PATCH' => 'm-patch', 'DELETE' => 'm-delete'];
  $methodCls = $methodColors[$method] ?? 'm-get';
@endphp

<div class="apidocs-endpoint" @if($id) id="{{ $id }}" @endif>
  <div class="apidocs-endpoint__head">
    <span class="apidocs-method {{ $methodCls }}">{{ $method }}</span>
    <code class="apidocs-path">{{ $path }}</code>
    @if($auth)
      <span class="apidocs-auth"><i class="fas fa-lock"></i> Auth</span>
    @else
      <span class="apidocs-auth apidocs-auth--public"><i class="fas fa-globe"></i> Public</span>
    @endif
    @if($name)
      <code class="apidocs-name">{{ $name }}</code>
    @endif
  </div>

  @if($desc)
    <p class="apidocs-endpoint__desc">{!! $desc !!}</p>
  @endif

  @if($request)
    <details class="apidocs-block" open>
      <summary><i class="fas fa-arrow-up"></i> Request body</summary>
      <pre><code>{{ $request }}</code></pre>
    </details>
  @endif

  @if($response)
    <details class="apidocs-block">
      <summary><i class="fas fa-arrow-down"></i> Response</summary>
      <pre><code>{{ $response }}</code></pre>
    </details>
  @endif
</div>
