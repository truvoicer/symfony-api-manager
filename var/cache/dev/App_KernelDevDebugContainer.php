<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerBd4Y2W9\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerBd4Y2W9/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerBd4Y2W9.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerBd4Y2W9\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerBd4Y2W9\App_KernelDevDebugContainer([
    'container.build_hash' => 'Bd4Y2W9',
    'container.build_id' => 'a3311aaa',
    'container.build_time' => 1599866395,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerBd4Y2W9');