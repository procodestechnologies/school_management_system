<x-error-page :code="$exception->getStatusCode()" icon="exclamation-triangle" title="Something went wrong on our end"
    message="We hit an unexpected snag while handling that request. It's not something you did — our team has been notified. Please try again in a moment." />
