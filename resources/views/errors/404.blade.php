@php $msg = $exception->getMessage(); @endphp
@include('errors._layout', ['code' => 404, 'heading' => 'Page not found', 'text' => ($msg && ! str_starts_with($msg, 'No query results')) ? $msg : 'The page you are looking for does not exist or was moved.'])
