<?php if($paginator->hasPages()): ?>
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; background: white; border-top: 1px solid #e2e8f0;">
        
        <?php if($paginator->onFirstPage()): ?>
            <button disabled style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; color: #94a3b8; cursor: not-allowed; font-size: 14px; font-weight: 500;">
                <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                Previous
            </button>
        <?php else: ?>
            <a href="<?php echo e($paginator->previousPageUrl()); ?>" style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #4f46e5; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                <i data-feather="chevron-left" style="width: 16px; height: 16px;"></i>
                Previous
            </a>
        <?php endif; ?>

        
        <div style="display: flex; gap: 6px; align-items: center;">
            <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <?php if(is_string($element)): ?>
                    <span style="padding: 8px 12px; color: #94a3b8; font-size: 14px;"><?php echo e($element); ?></span>
                <?php endif; ?>

                
                <?php if(is_array($element)): ?>
                    <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $paginator->currentPage()): ?>
                            <span style="padding: 8px 14px; background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); color: white; border-radius: 8px; font-size: 14px; font-weight: 600; min-width: 40px; text-align: center; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);"><?php echo e($page); ?></span>
                        <?php else: ?>
                            <a href="<?php echo e($url); ?>" style="padding: 8px 14px; background: white; border: 1px solid #e2e8f0; color: #64748b; border-radius: 8px; font-size: 14px; font-weight: 500; min-width: 40px; text-align: center; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1'" onmouseout="this.style.background='white'; this.style.borderColor='#e2e8f0'"><?php echo e($page); ?></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <?php if($paginator->hasMorePages()): ?>
            <a href="<?php echo e($paginator->nextPageUrl()); ?>" style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #4f46e5; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                Next
                <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
            </a>
        <?php else: ?>
            <button disabled style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; color: #94a3b8; cursor: not-allowed; font-size: 14px; font-weight: 500;">
                Next
                <i data-feather="chevron-right" style="width: 16px; height: 16px;"></i>
            </button>
        <?php endif; ?>
    </div>

    <script>
        // Re-initialize feather icons for pagination
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    </script>
<?php endif; ?>
<?php /**PATH C:\Users\v\.gemini\antigravity\scratch\dashboard-riyadlul-huda\resources\views/vendor/pagination/custom.blade.php ENDPATH**/ ?>