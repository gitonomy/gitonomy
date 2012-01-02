<?php

namespace Gitonomy\Bundle\CoreBundle\EventDispatcher;

final class GitonomyEvents
{
    const PROJECT_CREATE = 'gitonomy.project_create';
    const PROJECT_PUSH   = 'gitonomy.project_push';
    const PROJECT_DELETE = 'gitonomy.project_delete';
}
