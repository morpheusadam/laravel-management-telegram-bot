<div class="row" id="basic-table">
<div class="col-12">
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <!-- Table with outer spacing -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{__("Module")}}</th>
                            <th class="text-center">{{__("Limit")}}</th>
                            <th class="text-center">{{__("Used")}}</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $pricing_link = $parent_user_id==1 ? env('APP_URL').'/pricing' : route('pricing-plan');
                        $is_ppu = $is_agent && Auth()->user()->agent_has_ppu=='1';
                        if($is_ppu) $pricing_link = route('pricing-plan-ppu');
                        $i=0;
                        if($is_agent){
                            $i++;
                            $str_limit_user_agent = $limit_user_agent;
                            if($limit_user_agent==0) $str_limit_user_agent = "<span class='badge bg-success'>".__('Unlimited')."</span>";
                            echo "<tr>";
                            echo "<td>".$i."</td>";
                            echo "<td>".__('End-user')."</td>";
                            echo "<td class='text-center'>".$str_limit_user_agent."</td>";
                            echo "<td class='text-center'>".$user_count."</td>";
                        }
                        foreach($modules as $row)
                        {
                            $i++;
                            $row_class="";
                            $has_access = in_array($row->id,$user_module_ids);
                            echo "<tr>";
                            echo "<td>".$i."</td>";
                            echo "<td>".__($row->module_name)."</td>";

                            if(!$has_access) // no access
                            {
                                $str="<a class='badge bg-warning no-radius py-2' href='".$pricing_link."'><i class='fas fa-shopping-cart'></i> ".__("Upgrade to get access")."</a>";
                                echo "<td colspan='2' class='text-center'>{$str}</td>";
                            }
                            else
                            {
                                if($row->limit_enabled=='0') echo "<td colspan='2' class='text-center'>".__('No Limit Applicable')."</td>";
                                else{
                                    $extra_text = $monthly_limit[$row->id]>0 && $row->extra_text!="" ? " / ".__($row->extra_text) : '';

                                    $monthly_limit_subscriber = $monthly_limit[$row->id];
                                    $monthly_limit_subscriber .= $extra_text;

                                    if(!$is_agent)
                                    echo $monthly_limit[$row->id]>0
                                        ? "<td class='text-center'>". number_format($monthly_limit[$row->id])."</td>"
                                        : "<td class='text-center'><span class='badge bg-success'>".__('Unlimited')."</span></td>";

                                    if($is_agent){
                                        echo $monthly_limit_subscriber>0
                                        ? "<td class='text-center'>".number_format($monthly_limit_subscriber)."</td>"
                                        : "<td class='text-center'><span class='badge bg-success'>".__('Unlimited')."</span></td>";
                                    }
                                                                     
                                    echo isset($usage_info[$row->id]['usage_count'])
                                    ? "<td class='text-center'>".number_format($usage_info[$row->id]['usage_count'])."</td>"
                                    : "<td class='text-center'>0</td>";
                                

                                }

                            }
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
