<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerKl810gv\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerKl810gv/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerKl810gv.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerKl810gv\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerKl810gv\App_KernelDevDebugContainer([
    'container.build_hash' => 'Kl810gv',
    'container.build_id' => '93da40d2',
    'container.build_time' => 1710923796,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerKl810gv');
