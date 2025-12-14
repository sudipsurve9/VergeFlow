

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Manage Clients</h1>
                <a href="<?php echo e(route('super_admin.clients.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Client
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php if($clients->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Domain</th>
                                <th>Stats</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($client->id); ?></td>
                                <td>
                                    <strong><?php echo e($client->name); ?></strong>
                                    <?php if($client->theme): ?>
                                        <br><small class="text-muted">Theme: <?php echo e($client->theme); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($client->company_name); ?></td>
                                <td>
                                    <div><?php echo e($client->contact_email); ?></div>
                                    <?php if($client->contact_phone): ?>
                                        <small class="text-muted"><?php echo e($client->contact_phone); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($client->domain): ?>
                                        <a href="http://<?php echo e($client->domain); ?>" target="_blank"><?php echo e($client->domain); ?></a>
                                    <?php elseif($client->subdomain): ?>
                                        <a href="http://<?php echo e($client->subdomain); ?>.vault64.com" target="_blank"><?php echo e($client->subdomain); ?>.vault64.com</a>
                                    <?php else: ?>
                                        <span class="text-muted">No domain</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="text-xs font-weight-bold text-primary"><?php echo e($client->users_count); ?></div>
                                            <small class="text-muted">Users</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-xs font-weight-bold text-success"><?php echo e($client->products_count); ?></div>
                                            <small class="text-muted">Products</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-xs font-weight-bold text-info"><?php echo e($client->orders_count); ?></div>
                                            <small class="text-muted">Orders</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo e($client->is_active ? 'success' : 'danger'); ?>">
                                        <?php echo e($client->is_active ? 'Active' : 'Inactive'); ?>

                                    </span>
                                </td>
                                <td><?php echo e($client->created_at->format('M d, Y')); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('super_admin.clients.edit', $client)); ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteClient(<?php echo e($client->id); ?>)" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($clients->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No clients found</h4>
                    <p class="text-muted">Get started by creating your first client.</p>
                    <a href="<?php echo e(route('super_admin.clients.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Client
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteClientModal" tabindex="-1" aria-labelledby="deleteClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteClientModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this client? This action cannot be undone and will delete all associated data.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteClientForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete Client</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteClient(clientId) {
    if (confirm('Are you sure you want to delete this client? This will delete ALL associated data including users, products, orders, etc.')) {
        const form = document.getElementById('deleteClientForm');
        form.action = `/super-admin/clients/${clientId}`;
        form.submit();
    }
}
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.super_admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\VergeFlow\resources\views/super_admin/clients/index.blade.php ENDPATH**/ ?>