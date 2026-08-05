<x-error-page :code="$exception->getStatusCode()" icon="exclamation-circle" title="Something's not right with that request"
    message="We couldn't process that request as-is. Try going back and starting again, or head somewhere familiar." />
